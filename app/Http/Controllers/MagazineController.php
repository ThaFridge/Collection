<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MagazineController extends Controller
{
    public function index(Request $request)
    {
        $query = Magazine::query();

        if ($q = $request->input('q')) {
            $query->where(function ($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%")
                   ->orWhere('publisher', 'like', "%{$q}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($year = $request->input('year')) {
            $query->where('year', $year);
        }

        $sort = $request->input('sort', 'year-desc');
        match ($sort) {
            'title' => $query->orderBy('title'),
            'title-desc' => $query->orderByDesc('title'),
            'year' => $query->orderBy('year')->orderBy('issue_number'),
            default => $query->orderByDesc('year')->orderByDesc('issue_number'),
        };

        $years = Magazine::select('year')->distinct()->orderByDesc('year')->pluck('year');
        $magazines = $query->paginate(24);
        $total = Magazine::count();

        return view('magazines.index', compact('magazines', 'years', 'total'));
    }

    public function create()
    {
        return view('magazines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:magazine,manual',
            'title' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'issue_number' => 'nullable|string|max:50',
            'publication_date' => 'nullable|date',
            'year' => 'required|integer|min:1970|max:2099',
            'pdf' => 'required|file|mimes:pdf|max:204800',
            'cover' => 'nullable|image|max:5120',
            'notes' => 'nullable|string',
        ]);

        $pdfPath = $request->file('pdf')->storeAs('magazines/pdfs', $request->file('pdf')->getClientOriginalName(), 'public');

        if (!$pdfPath) {
            return back()->withInput()->with('error', 'PDF upload mislukt.');
        }

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('magazines/covers', 'public');
        } else {
            $coverPath = $this->extractCoverFromPdf(Storage::disk('public')->path($pdfPath));
        }

        $magazine = Magazine::create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'publisher' => $validated['publisher'],
            'issue_number' => $validated['issue_number'],
            'publication_date' => $validated['publication_date'],
            'year' => $validated['year'],
            'pdf_path' => $pdfPath,
            'cover_image_path' => $coverPath,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('magazines.show', $magazine)->with('success', 'Magazine opgeslagen!');
    }

    public function show(Magazine $magazine)
    {
        return view('magazines.show', compact('magazine'));
    }

    public function edit(Magazine $magazine)
    {
        return view('magazines.edit', compact('magazine'));
    }

    public function update(Request $request, Magazine $magazine)
    {
        $validated = $request->validate([
            'type' => 'required|in:magazine,manual',
            'title' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'issue_number' => 'nullable|string|max:50',
            'publication_date' => 'nullable|date',
            'year' => 'required|integer|min:1970|max:2099',
            'cover' => 'nullable|image|max:5120',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('cover')) {
            if ($magazine->cover_image_path) {
                Storage::disk('public')->delete($magazine->cover_image_path);
            }
            $validated['cover_image_path'] = $request->file('cover')->store('magazines/covers', 'public');
        }

        $magazine->update(array_filter($validated, fn ($k) => $k !== 'cover', ARRAY_FILTER_USE_KEY));

        return redirect()->route('magazines.show', $magazine)->with('success', 'Magazine bijgewerkt!');
    }

    public function destroy(Magazine $magazine)
    {
        Storage::disk('public')->delete($magazine->pdf_path);
        if ($magazine->cover_image_path) {
            Storage::disk('public')->delete($magazine->cover_image_path);
        }
        $magazine->delete();

        return redirect()->route('magazines.index')->with('success', 'Magazine verwijderd!');
    }

    private function extractCoverFromPdf(string $pdfAbsolutePath): ?string
    {
        if (!extension_loaded('imagick')) {
            return null;
        }

        try {
            $imagick = new \Imagick();
            $imagick->setResolution(150, 150);
            $imagick->readImage($pdfAbsolutePath . '[0]');
            $imagick->setImageFormat('jpg');
            $imagick->thumbnailImage(400, 0);

            $filename = 'magazines/covers/' . uniqid('cover_') . '.jpg';
            Storage::disk('public')->put($filename, $imagick->getImageBlob());
            $imagick->clear();
            $imagick->destroy();

            return $filename;
        } catch (\Exception $e) {
            \Log::error('Cover extract failed: ' . $e->getMessage());
            return null;
        }
    }
}
