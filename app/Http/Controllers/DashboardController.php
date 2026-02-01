<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlatform;
use App\Models\LegoSet;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Game stats
        $gameStats = [
            'total' => Game::inCollection()->count(),
            'wishlist' => Game::onWishlist()->count(),
            'total_value' => GamePlatform::collection()->sum('purchase_price'),
            'physical' => GamePlatform::collection()->where('format', 'physical')->count(),
            'digital' => GamePlatform::collection()->where('format', 'digital')->count(),
            'both' => GamePlatform::collection()->where('format', 'both')->count(),
        ];

        $gamesByPlatform = GamePlatform::collection()
            ->select('platform', DB::raw('count(*) as count'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('count')
            ->get();

        $gamesByCompletion = GamePlatform::collection()
            ->select('completion_status', DB::raw('count(*) as count'))
            ->groupBy('completion_status')
            ->get()
            ->pluck('count', 'completion_status');

        $gamesByGenre = Game::inCollection()
            ->select('genre', DB::raw('count(*) as count'))
            ->whereNotNull('genre')
            ->where('genre', '!=', '')
            ->groupBy('genre')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // LEGO stats
        $legoStats = [
            'total' => LegoSet::collection()->count(),
            'wishlist' => LegoSet::wishlist()->count(),
            'total_value' => LegoSet::collection()->sum('purchase_price'),
            'total_retail' => LegoSet::collection()->sum('retail_price'),
            'total_pieces' => LegoSet::collection()->sum('piece_count'),
            'total_minifigs' => LegoSet::collection()->sum('minifigure_count'),
        ];

        $legoByTheme = LegoSet::collection()
            ->select('theme', DB::raw('count(*) as count'), DB::raw('sum(piece_count) as pieces'))
            ->whereNotNull('theme')
            ->groupBy('theme')
            ->orderByDesc('count')
            ->get();

        $legoByBuildStatus = LegoSet::collection()
            ->select('build_status', DB::raw('count(*) as count'))
            ->groupBy('build_status')
            ->get()
            ->pluck('count', 'build_status');

        return view('dashboard.index', compact(
            'gameStats', 'gamesByPlatform', 'gamesByCompletion', 'gamesByGenre',
            'legoStats', 'legoByTheme', 'legoByBuildStatus'
        ));
    }
}
