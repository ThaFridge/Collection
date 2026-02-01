<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlatform;
use App\Models\Tag;
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

        $stats = [
            'total' => Game::inCollection()->count(),
            'total_value' => GamePlatform::collection()->sum('purchase_price'),
            'physical' => GamePlatform::collection()->where('format', 'physical')->count(),
            'digital' => GamePlatform::collection()->where('format', 'digital')->count(),
        ];

        return view('games.index', compact('games', 'platforms', 'stats'));
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
            'platform' => 'required|string|max:50',
            'format' => 'required|in:physical,digital,both',
            'status' => 'required|in:collection,wishlist',
            'completion_status' => 'required|in:not_played,playing,completed,platinum',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
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

        // Add platform
        $game->platforms()->create($request->only([
            'platform', 'format', 'status', 'completion_status',
            'purchase_price', 'purchase_date', 'condition', 'barcode',
        ]));

        return redirect()->route('games.show', $game)->with('success', 'Game toegevoegd!');
    }

    public function show(Game $game)
    {
        $game->load('platforms', 'images', 'tags');
        $allTags = Tag::orderBy('name')->get();

        return view('games.show', compact('game', 'allTags'));
    }

    public function edit(Game $game)
    {
        $game->load('platforms');
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
            'condition' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
        ]);

        $game->platforms()->create($request->only([
            'platform', 'format', 'status', 'completion_status',
            'purchase_price', 'purchase_date', 'condition', 'barcode',
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
            'condition' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
        ]);

        $platform->update($request->only([
            'status', 'completion_status',
            'purchase_price', 'purchase_date', 'condition', 'barcode',
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
