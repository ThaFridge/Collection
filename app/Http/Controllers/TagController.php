<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Game;
use App\Models\LegoSet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount(['games', 'legoSets'])->orderBy('name')->get();
        return view('tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:tags,name']);
        Tag::create(['name' => $request->name, 'slug' => Str::slug($request->name)]);
        return back()->with('success', 'Tag aangemaakt!');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back()->with('success', 'Tag verwijderd!');
    }

    // Attach/detach tags to games
    public function toggleGameTag(Request $request, Game $game)
    {
        $request->validate(['tag_id' => 'required|exists:tags,id']);
        $game->tags()->toggle($request->tag_id);
        return back()->with('success', 'Tag bijgewerkt!');
    }

    // Attach/detach tags to LEGO sets
    public function toggleLegoTag(Request $request, LegoSet $legoSet)
    {
        $request->validate(['tag_id' => 'required|exists:tags,id']);
        $legoSet->tags()->toggle($request->tag_id);
        return back()->with('success', 'Tag bijgewerkt!');
    }
}
