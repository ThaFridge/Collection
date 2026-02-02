<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlatform;
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

    // CSV Export Games (flattened: one row per platform entry)
    public function exportGamesCsv()
    {
        $platforms = GamePlatform::with('game')->get();

        $columns = [
            'game_name', 'platform', 'format', 'status', 'completion_status',
            'genre', 'developer', 'publisher', 'release_date', 'purchase_price',
            'purchase_date', 'rating', 'barcode', 'notes',
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $columns, ';');

        foreach ($platforms as $p) {
            fputcsv($output, [
                $p->game->name,
                $p->platform,
                $p->format,
                $p->status,
                $p->completion_status,
                $p->game->genre,
                $p->game->developer,
                $p->game->publisher,
                $p->game->release_date?->format('Y-m-d'),
                $p->purchase_price,
                $p->purchase_date?->format('Y-m-d'),
                $p->game->rating,
                $p->barcode,
                $p->game->notes,
            ], ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

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
            'games' => Game::with('platforms')->get()->toArray(),
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

        // Import games (with platforms)
        foreach ($data['games'] ?? [] as $gameData) {
            $platforms = $gameData['platforms'] ?? [];
            unset($gameData['id'], $gameData['created_at'], $gameData['updated_at'], $gameData['platforms']);

            if (empty($gameData['slug'])) {
                $gameData['slug'] = Str::slug($gameData['name'] ?? 'game');
            }

            $game = Game::where('name', $gameData['name'] ?? '')->first();
            if (!$game) {
                $game = Game::create($gameData);
            }

            foreach ($platforms as $pData) {
                unset($pData['id'], $pData['created_at'], $pData['updated_at'], $pData['game_id']);
                $exists = $game->platforms()
                    ->where('platform', $pData['platform'] ?? '')
                    ->where('format', $pData['format'] ?? 'physical')
                    ->exists();
                if ($exists) {
                    $imported['skipped']++;
                } else {
                    $game->platforms()->create($pData);
                    $imported['games']++;
                }
            }
        }

        // Import LEGO sets
        foreach ($data['lego_sets'] ?? [] as $legoData) {
            unset($legoData['id'], $legoData['created_at'], $legoData['updated_at']);

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
            "Import voltooid: {$imported['games']} game-platforms, {$imported['lego']} LEGO sets geïmporteerd. {$imported['skipped']} duplicaten overgeslagen."
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
