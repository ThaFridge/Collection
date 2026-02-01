<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\LegoSet;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'gameCount' => Game::collection()->count(),
            'gameWishlist' => Game::wishlist()->count(),
            'gameValue' => Game::collection()->sum('purchase_price'),
            'legoCount' => LegoSet::collection()->count(),
            'legoPieces' => LegoSet::collection()->sum('piece_count'),
            'legoValue' => LegoSet::collection()->sum('purchase_price'),
        ]);
    }
}
