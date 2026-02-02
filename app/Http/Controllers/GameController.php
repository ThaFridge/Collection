<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlatform;
use App\Models\Tag;
use App\Services\ApiProviders\RawgProvider;
use App\Services\ApiProviders\IgdbProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::inCollection()->with('platforms');

        if ($request->filled('platform')) {
            $query->whereHas('platforms', fn ($q) => $q->where('platform', $request->platform)->where('status', 'collection'));
        }
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }
        if ($request->filled('completion_status')) {
            $query->whereHas('platforms', fn ($q) => $q->where('completion_status', $request->completion_status)->where('status', 'collection'));
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $sortParam = $request->get('sort', 'name');
        $sortParts = explode('-', $sortParam, 2);
        $sort = $sortParts[0];
        $dir = $sortParts[1] ?? 'asc';
        $allowed = ['name', 'created_at', 'release_date'];
        if (!in_array($sort, $allowed)) { $sort = 'name'; $dir = 'asc'; }
        $query->orderBy($sort, $dir);

        $games = $query->paginate(24);

        $platforms = GamePlatform::collection()
            ->distinct()->pluck('platform')->sort()->values();

        $genres = Game::inCollection()->whereNotNull('genre')
            ->where('genre', '!=', '')->distinct()->pluck('genre')->sort()->values();

        $stats = [
            'total' => Game::inCollection()->count(),
            'total_value' => GamePlatform::collection()->sum('purchase_price'),
            'physical' => GamePlatform::collection()->where('format', 'physical')->count(),
            'digital' => GamePlatform::collection()->where('format', 'digital')->count(),
        ];

        return view('games.index', compact('games', 'platforms', 'genres', 'stats'));
    }

    public function create()
    {
        return view('games.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:100',
            'release_date' => 'nullable|date',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image_url' => 'nullable|url|max:500',
            'rating' => 'nullable|integer|min:1|max:10',
            'notes' => 'nullable|string',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|max:50',
            'format' => 'required|in:physical,digital,both',
            'status' => 'required|in:collection,wishlist',
            'completion_status' => 'required|in:not_played,playing,completed,platinum',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
        ]);

        $gameData = $request->only([
            'name', 'genre', 'release_date', 'developer', 'publisher',
            'description', 'cover_image_url', 'rating', 'notes',
        ]);
        $gameData['slug'] = Str::slug($gameData['name']);

        if (!empty($gameData['cover_image_url'])) {
            $gameData['cover_image_path'] = $this->downloadCover($gameData['cover_image_url']);
        }

        // Check if game already exists (by name)
        $game = Game::where('name', $request->name)->first();
        if ($game) {
            $game->update($gameData);
        } else {
            $game = Game::create($gameData);
        }

        // Add platform entries for each selected platform
        $platformData = $request->only([
            'format', 'status', 'completion_status',
            'purchase_price', 'purchase_date',
        ]);

        foreach ($request->platforms as $platform) {
            $game->platforms()->create(array_merge($platformData, ['platform' => $platform]));
        }

        // Fetch achievements and screenshots from RAWG
        $this->fetchAchievements($game);
        $this->fetchScreenshots($game);

        return redirect()->route('games.show', $game)->with('success', 'Game toegevoegd!');
    }

    public function show(Game $game)
    {
        // Auto-fetch achievements and screenshots if not yet fetched
        if ($game->external_api_id && in_array($game->external_api_source, ['rawg', 'igdb'])) {
            if (!$game->achievements_fetched) {
                $this->fetchAchievements($game);
            }
            if ($game->images()->count() === 0) {
                $this->fetchScreenshots($game);
            }
            $game->refresh();
        }

        $game->load('platforms', 'images', 'tags', 'achievements');
        $allTags = Tag::orderBy('name')->get();

        return view('games.show', compact('game', 'allTags'));
    }

    public function edit(Game $game)
    {
        $game->load('platforms', 'images');
        return view('games.edit', compact('game'));
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:100',
            'release_date' => 'nullable|date',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image_url' => 'nullable|url|max:500',
            'rating' => 'nullable|integer|min:1|max:10',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only([
            'name', 'genre', 'release_date', 'developer', 'publisher',
            'description', 'cover_image_url', 'rating', 'notes',
        ]);

        if (!empty($data['cover_image_url']) && $data['cover_image_url'] !== $game->cover_image_url) {
            $data['cover_image_path'] = $this->downloadCover($data['cover_image_url']);
        }

        $game->update($data);

        return redirect()->route('games.show', $game)->with('success', 'Game bijgewerkt!');
    }

    public function destroy(Game $game)
    {
        if ($game->cover_image_path && Storage::disk('public')->exists($game->cover_image_path)) {
            Storage::disk('public')->delete($game->cover_image_path);
        }
        $game->delete();

        return redirect()->route('games.index')->with('success', 'Game verwijderd!');
    }

    public function wishlist(Request $request)
    {
        $platforms = GamePlatform::wishlist()
            ->with('game')
            ->orderBy('created_at', 'desc')
            ->paginate(24);

        return view('games.wishlist', compact('platforms'));
    }

    public function addPlatform(Request $request, Game $game)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'format' => 'required|in:physical,digital,both',
            'status' => 'required|in:collection,wishlist',
            'completion_status' => 'required|in:not_played,playing,completed,platinum',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'barcode' => 'nullable|string|max:50',
        ]);

        $game->platforms()->create($request->only([
            'platform', 'format', 'status', 'completion_status',
            'purchase_price', 'purchase_date', 'barcode',
        ]));

        return back()->with('success', 'Platform toegevoegd!');
    }

    public function updatePlatform(Request $request, GamePlatform $platform)
    {
        $request->validate([
            'status' => 'required|in:collection,wishlist',
            'completion_status' => 'required|in:not_played,playing,completed,platinum',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'barcode' => 'nullable|string|max:50',
        ]);

        $platform->update($request->only([
            'status', 'completion_status',
            'purchase_price', 'purchase_date', 'barcode',
        ]));

        return back()->with('success', 'Platform bijgewerkt!');
    }

    public function destroyPlatform(GamePlatform $platform)
    {
        $game = $platform->game;
        $platform->delete();

        if ($game->platforms()->count() === 0) {
            if ($game->cover_image_path && Storage::disk('public')->exists($game->cover_image_path)) {
                Storage::disk('public')->delete($game->cover_image_path);
            }
            $game->delete();
            return redirect()->route('games.index')->with('success', 'Platform en game verwijderd!');
        }

        return back()->with('success', 'Platform verwijderd!');
    }

    public function togglePlatformStatus(GamePlatform $platform)
    {
        $platform->status = $platform->status === 'collection' ? 'wishlist' : 'collection';
        $platform->save();

        return back()->with('success', 'Status gewijzigd!');
    }

    public function checkDuplicate(Request $request)
    {
        $query = GamePlatform::where('platform', $request->platform)
            ->where('format', $request->format)
            ->whereHas('game', fn ($q) => $q->where('name', $request->name));
        return response()->json(['exists' => $query->exists()]);
    }

    public function refreshAchievements(Game $game)
    {
        $game->achievements()->delete();
        $game->update(['achievements_fetched' => false]);
        $this->fetchAchievements($game);

        return back()->with('success', 'Achievements vernieuwd!');
    }

    private function getProvider(Game $game): RawgProvider|IgdbProvider|null
    {
        return match ($game->external_api_source) {
            'rawg' => app(RawgProvider::class),
            'igdb' => app(IgdbProvider::class),
            default => null,
        };
    }

    private function fetchAchievements(Game $game): void
    {
        if (!$game->external_api_id || !in_array($game->external_api_source, ['rawg', 'igdb'])) {
            $game->update(['achievements_fetched' => true, 'achievements_supported' => false]);
            return;
        }

        try {
            $provider = $this->getProvider($game);
            if (!$provider || !$provider->isConfigured()) return;

            $achievements = $provider->fetchAchievements($game->external_api_id);

            if ($achievements === null || empty($achievements)) {
                $game->update(['achievements_fetched' => true, 'achievements_supported' => count($achievements ?? []) > 0]);
                return;
            }

            foreach ($achievements as $a) {
                $game->achievements()->create($a);
            }

            $game->update(['achievements_fetched' => true, 'achievements_supported' => true]);
        } catch (\Exception $e) {
            // Silently fail - achievements are not critical
        }
    }

    public function uploadScreenshot(Request $request, Game $game)
    {
        $request->validate([
            'screenshot' => 'required|image|max:5120',
        ]);

        if ($game->images()->count() >= 8) {
            return back()->with('error', 'Maximaal 8 screenshots per game.');
        }

        $path = $request->file('screenshot')->store('screenshots/' . $game->id, 'public');
        $sortOrder = $game->images()->max('sort_order') + 1;

        $game->images()->create([
            'image_path' => $path,
            'type' => 'screenshot',
            'sort_order' => $sortOrder,
        ]);

        return back()->with('success', 'Screenshot toegevoegd!');
    }

    public function deleteScreenshot(Game $game, \App\Models\GameImage $image)
    {
        if ($image->game_id !== $game->id) abort(404);

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();

        return back()->with('success', 'Screenshot verwijderd!');
    }

    private function fetchScreenshots(Game $game): void
    {
        if (!$game->external_api_id || !in_array($game->external_api_source, ['rawg', 'igdb'])) return;

        try {
            $provider = $this->getProvider($game);
            if (!$provider || !$provider->isConfigured()) return;

            $urls = $provider->fetchScreenshots($game->external_api_id, 8);

            foreach ($urls as $i => $url) {
                $contents = file_get_contents($url);
                if ($contents === false) continue;

                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = 'screenshots/' . $game->id . '/' . Str::uuid() . '.' . $extension;
                Storage::disk('public')->put($filename, $contents);

                $game->images()->create([
                    'image_path' => $filename,
                    'type' => 'screenshot',
                    'sort_order' => $i,
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    private function downloadCover(string $url): ?string
    {
        try {
            $contents = file_get_contents($url);
            if ($contents === false) return null;

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'covers/' . Str::uuid() . '.' . $extension;
            Storage::disk('public')->put($filename, $contents);

            return $filename;
        } catch (\Exception $e) {
            return null;
        }
    }
}
