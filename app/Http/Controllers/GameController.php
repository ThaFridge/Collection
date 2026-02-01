<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\GameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::collection();

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }
        if ($request->filled('completion_status')) {
            $query->where('completion_status', $request->completion_status);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $sortParam = $request->get('sort', 'name');
        $sortParts = explode('-', $sortParam, 2);
        $sort = $sortParts[0];
        $dir = $sortParts[1] ?? 'asc';
        $allowed = ['name', 'created_at', 'purchase_price', 'release_date'];
        if (!in_array($sort, $allowed)) { $sort = 'name'; $dir = 'asc'; }
        $query->orderBy($sort, $dir);

        $games = $query->paginate(24);
        $platforms = Game::collection()->whereNotNull('platform')->distinct()->pluck('platform')->sort();
        $genres = Game::collection()->whereNotNull('genre')->distinct()->pluck('genre')->sort();

        $stats = [
            'total' => Game::collection()->count(),
            'total_value' => Game::collection()->sum('purchase_price'),
            'physical' => Game::collection()->where('format', 'physical')->count(),
            'digital' => Game::collection()->where('format', 'digital')->count(),
        ];

        return view('games.index', compact('games', 'platforms', 'genres', 'stats'));
    }

    public function create()
    {
        return view('games.create');
    }

    public function store(GameRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if (!empty($data['cover_image_url'])) {
            $data['cover_image_path'] = $this->downloadCover($data['cover_image_url']);
        }

        Game::create($data);

        return redirect()->route('games.index')->with('success', 'Game toegevoegd!');
    }

    public function show(Game $game)
    {
        $game->load('images', 'tags');
        $otherPlatforms = Game::where('name', $game->name)
            ->where('id', '!=', $game->id)
            ->get();
        $allTags = \App\Models\Tag::orderBy('name')->get();

        return view('games.show', compact('game', 'otherPlatforms', 'allTags'));
    }

    public function edit(Game $game)
    {
        return view('games.edit', compact('game'));
    }

    public function update(GameRequest $request, Game $game)
    {
        $data = $request->validated();

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

    public function toggleStatus(Game $game)
    {
        $game->status = $game->status === 'collection' ? 'wishlist' : 'collection';
        $game->save();

        return back()->with('success', 'Status gewijzigd!');
    }

    public function wishlist(Request $request)
    {
        $games = Game::wishlist()->orderBy('name')->paginate(24);
        return view('games.wishlist', compact('games'));
    }

    public function checkDuplicate(Request $request)
    {
        $exists = Game::where('name', $request->name)
            ->where('platform', $request->platform)
            ->where('format', $request->format)
            ->exists();
        return response()->json(['exists' => $exists]);
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
