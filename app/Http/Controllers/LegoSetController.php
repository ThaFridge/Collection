<?php

namespace App\Http\Controllers;

use App\Models\LegoSet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LegoSetController extends Controller
{
    public function index(Request $request)
    {
        $query = LegoSet::collection();

        if ($request->filled('theme')) {
            $query->where('theme', $request->theme);
        }
        if ($request->filled('build_status')) {
            $query->where('build_status', $request->build_status);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('set_number', 'like', '%' . $request->q . '%');
            });
        }

        $sortParam = $request->get('sort', 'name');
        $sortParts = explode('-', $sortParam, 2);
        $sort = $sortParts[0];
        $dir = $sortParts[1] ?? 'asc';
        $allowed = ['name', 'created_at', 'purchase_price', 'piece_count', 'release_year'];
        if (!in_array($sort, $allowed)) { $sort = 'name'; $dir = 'asc'; }
        $query->orderBy($sort, $dir);

        $sets = $query->paginate(24);
        $themes = LegoSet::collection()->whereNotNull('theme')->distinct()->pluck('theme')->sort();

        $stats = [
            'total' => LegoSet::collection()->count(),
            'total_value' => LegoSet::collection()->sum('purchase_price'),
            'total_pieces' => LegoSet::collection()->sum('piece_count'),
            'built' => LegoSet::collection()->whereIn('build_status', ['built', 'displayed'])->count(),
        ];

        return view('lego.index', compact('sets', 'themes', 'stats'));
    }

    public function create()
    {
        return view('lego.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'set_number' => 'required|string|max:20|unique:lego_sets,set_number',
            'name' => 'required|string|max:255',
            'theme' => 'nullable|string|max:100',
            'subtheme' => 'nullable|string|max:100',
            'piece_count' => 'nullable|integer|min:0',
            'minifigure_count' => 'nullable|integer|min:0',
            'image_url' => 'nullable|url|max:500',
            'release_year' => 'nullable|integer|min:1949|max:2030',
            'retail_price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'nullable|string|max:50',
            'status' => 'required|in:collection,wishlist',
            'build_status' => 'required|in:not_built,in_progress,built,displayed',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['image_url'])) {
            $data['image_path'] = $this->downloadImage($data['image_url']);
        }

        $data['slug'] = Str::slug($data['name'] . '-' . $data['set_number']);
        $data['instructions_url'] = 'https://www.lego.com/nl-nl/service/buildinginstructions/' . $data['set_number'];

        LegoSet::create($data);

        return redirect()->route('lego.index')->with('success', 'LEGO set toegevoegd!');
    }

    public function show(LegoSet $legoSet)
    {
        $legoSet->load('images', 'tags');
        $allTags = \App\Models\Tag::orderBy('name')->get();
        return view('lego.show', compact('legoSet', 'allTags'));
    }

    public function edit(LegoSet $legoSet)
    {
        return view('lego.edit', compact('legoSet'));
    }

    public function update(Request $request, LegoSet $legoSet)
    {
        $data = $request->validate([
            'set_number' => 'required|string|max:20|unique:lego_sets,set_number,' . $legoSet->id,
            'name' => 'required|string|max:255',
            'theme' => 'nullable|string|max:100',
            'subtheme' => 'nullable|string|max:100',
            'piece_count' => 'nullable|integer|min:0',
            'minifigure_count' => 'nullable|integer|min:0',
            'image_url' => 'nullable|url|max:500',
            'release_year' => 'nullable|integer|min:1949|max:2030',
            'retail_price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'nullable|string|max:50',
            'status' => 'required|in:collection,wishlist',
            'build_status' => 'required|in:not_built,in_progress,built,displayed',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['image_url']) && $data['image_url'] !== $legoSet->image_url) {
            $data['image_path'] = $this->downloadImage($data['image_url']);
        }

        $data['instructions_url'] = 'https://www.lego.com/nl-nl/service/buildinginstructions/' . $data['set_number'];

        $legoSet->update($data);

        return redirect()->route('lego.show', $legoSet)->with('success', 'LEGO set bijgewerkt!');
    }

    public function destroy(LegoSet $legoSet)
    {
        if ($legoSet->image_path && Storage::disk('public')->exists($legoSet->image_path)) {
            Storage::disk('public')->delete($legoSet->image_path);
        }
        $legoSet->delete();

        return redirect()->route('lego.index')->with('success', 'LEGO set verwijderd!');
    }

    public function toggleStatus(LegoSet $legoSet)
    {
        $legoSet->status = $legoSet->status === 'collection' ? 'wishlist' : 'collection';
        $legoSet->save();

        return back()->with('success', 'Status gewijzigd!');
    }

    public function updateBuildStatus(Request $request, LegoSet $legoSet)
    {
        $request->validate(['build_status' => 'required|in:not_built,in_progress,built,displayed']);
        $legoSet->update(['build_status' => $request->build_status]);

        return back()->with('success', 'Bouwstatus bijgewerkt!');
    }

    public function wishlist()
    {
        $sets = LegoSet::wishlist()->orderBy('name')->paginate(24);
        return view('lego.wishlist', compact('sets'));
    }

    public function checkDuplicate(Request $request)
    {
        $exists = LegoSet::where('set_number', $request->set_number)->exists();
        return response()->json(['exists' => $exists]);
    }

    private function downloadImage(string $url): ?string
    {
        try {
            $contents = file_get_contents($url);
            if ($contents === false) return null;

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'lego/' . Str::uuid() . '.' . $extension;
            Storage::disk('public')->put($filename, $contents);

            return $filename;
        } catch (\Exception $e) {
            return null;
        }
    }
}
