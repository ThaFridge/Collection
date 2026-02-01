<?php

namespace App\Http\Controllers;

use App\Services\LegoSearchService;
use Illuminate\Http\Request;

class LegoSearchController extends Controller
{
    public function search(Request $request, LegoSearchService $service)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $results = $service->search($request->q);

        return response()->json(
            collect($results)->map(fn($r) => $r->toArray())->values()
        );
    }
}
