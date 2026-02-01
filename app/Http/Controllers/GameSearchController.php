<?php

namespace App\Http\Controllers;

use App\Services\GameSearchService;
use Illuminate\Http\Request;

class GameSearchController extends Controller
{
    public function search(Request $request, GameSearchService $service)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $results = $service->search(
            $request->q,
            $request->platform
        );

        return response()->json(
            collect($results)->map(fn($r) => $r->toArray())->values()
        );
    }
}
