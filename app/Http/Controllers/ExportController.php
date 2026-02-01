<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\LegoSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    public function index()
    {
        return view('export.index');
    }

    // CSV Export Games
    public function exportGamesCsv()
    {
        $games = Game::all();
        $csv = $this->buildCsv($games, [
            'id', 'name', 'platform', 'format', 'status', 'completion_status',
            'genre', 'developer', 'publisher', 'release_date', 'purchase_price',
            'purchase_date', 'condition', 'rating', 'barcode', 'notes',
        ]);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="gamevault-games-' . date('Y-m-d') . '.csv"',
        ]);
    }

    // CSV Export LEGO
    public function exportLegoCsv()
    {
        $sets = LegoSet::all();
        $csv = $this->buildCsv($sets, [
            'id', 'set_number', 'name', 'theme', 'subtheme', 'status', 'build_status',
            'piece_count', 'minifigure_count', 'release_year', 'retail_price',
            'purchase_price', 'purchase_date', 'condition', 'notes',
        ]);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="gamevault-lego-' . date('Y-m-d') . '.csv"',
        ]);
    }

    // JSON Export all
    public function exportJson()
    {
        $data = [
            'exported_at' => now()->toIso8601String(),
            'games' => Game::all()->toArray(),
            'lego_sets' => LegoSet::all()->toArray(),
        ];

        return Response::make(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="gamevault-backup-' . date('Y-m-d') . '.json"',
        ]);
    }

    // JSON Import
    public function importJson(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:json,txt|max:10240']);

        $content = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($content, true);

        if (!$data || (!isset($data['games']) && !isset($data['lego_sets']))) {
            return back()->with('error', 'Ongeldig bestandsformaat.');
        }

        $imported = ['games' => 0, 'lego' => 0, 'skipped' => 0];

        // Import games
        foreach ($data['games'] ?? [] as $gameData) {
            unset($gameData['id'], $gameData['created_at'], $gameData['updated_at']);

            // Duplicate check: name + platform + format
            $exists = Game::where('name', $gameData['name'] ?? '')
                ->where('platform', $gameData['platform'] ?? null)
                ->where('format', $gameData['format'] ?? 'physical')
                ->exists();

            if ($exists) {
                $imported['skipped']++;
                continue;
            }

            if (empty($gameData['slug'])) {
                $gameData['slug'] = Str::slug($gameData['name'] ?? 'game');
            }

            Game::create($gameData);
            $imported['games']++;
        }

        // Import LEGO sets
        foreach ($data['lego_sets'] ?? [] as $legoData) {
            unset($legoData['id'], $legoData['created_at'], $legoData['updated_at']);

            // Duplicate check: set_number
            if (LegoSet::where('set_number', $legoData['set_number'] ?? '')->exists()) {
                $imported['skipped']++;
                continue;
            }

            if (empty($legoData['slug'])) {
                $legoData['slug'] = Str::slug(($legoData['name'] ?? 'set') . '-' . ($legoData['set_number'] ?? ''));
            }

            LegoSet::create($legoData);
            $imported['lego']++;
        }

        return back()->with('success',
            "Import voltooid: {$imported['games']} games, {$imported['lego']} LEGO sets geïmporteerd. {$imported['skipped']} duplicaten overgeslagen."
        );
    }

    private function buildCsv($items, array $columns): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $columns, ';');

        foreach ($items as $item) {
            $row = [];
            foreach ($columns as $col) {
                $val = $item->{$col};
                $row[] = $val instanceof \DateTimeInterface ? $val->format('Y-m-d') : $val;
            }
            fputcsv($output, $row, ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
