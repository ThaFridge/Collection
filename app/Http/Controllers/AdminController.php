<?php

namespace App\Http\Controllers;

use App\Models\ApiProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function index()
    {
        $providers = ApiProvider::orderByDesc('priority')->get();
        $dbInfo = [
            'driver' => config('database.default'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
        ];

        return view('admin.index', compact('providers', 'dbInfo'));
    }

    public function updateProvider(Request $request, ApiProvider $provider)
    {
        $request->validate([
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'api_key' => 'nullable|string',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
        ]);

        $credentials = is_array($provider->credentials_json) ? $provider->credentials_json : [];
        if ($request->filled('api_key')) $credentials['api_key'] = $request->api_key;
        if ($request->filled('client_id')) $credentials['client_id'] = $request->client_id;
        if ($request->filled('client_secret')) $credentials['client_secret'] = $request->client_secret;

        $provider->update([
            'is_active' => $request->boolean('is_active'),
            'priority' => $request->integer('priority', $provider->priority),
            'credentials_json' => $credentials,
        ]);

        return back()->with('success', $provider->name . ' bijgewerkt!');
    }

    public function testProvider(ApiProvider $provider)
    {
        $credentials = is_array($provider->credentials_json) ? $provider->credentials_json : [];
        $result = ['success' => false, 'message' => 'Onbekende provider'];

        try {
            switch ($provider->slug) {
                case 'rawg':
                    $apiKey = $credentials['api_key'] ?? null;
                    if (!$apiKey) {
                        $result = ['success' => false, 'message' => 'Geen API key ingesteld'];
                        break;
                    }
                    $response = Http::timeout(10)->get('https://api.rawg.io/api/games', [
                        'key' => $apiKey,
                        'search' => 'zelda',
                        'page_size' => 1,
                    ]);
                    if ($response->successful() && !empty($response->json('results'))) {
                        $result = ['success' => true, 'message' => 'Verbinding OK — ' . $response->json('results.0.name')];
                    } else {
                        $result = ['success' => false, 'message' => 'API error: ' . ($response->json('error') ?? $response->status())];
                    }
                    break;

                case 'igdb':
                    $clientId = $credentials['client_id'] ?? null;
                    $clientSecret = $credentials['client_secret'] ?? null;
                    if (!$clientId || !$clientSecret) {
                        $result = ['success' => false, 'message' => 'Client ID en/of Secret ontbreekt'];
                        break;
                    }

                    // Step 1: Get Twitch OAuth token
                    $tokenResponse = Http::timeout(10)->post('https://id.twitch.tv/oauth2/token', [
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret,
                        'grant_type' => 'client_credentials',
                    ]);

                    if (!$tokenResponse->successful()) {
                        $result = ['success' => false, 'message' => 'Twitch auth mislukt: ' . ($tokenResponse->json('message') ?? $tokenResponse->status())];
                        break;
                    }

                    $token = $tokenResponse->json('access_token');

                    // Step 2: Test IGDB API
                    $igdbResponse = Http::timeout(10)
                        ->withHeaders([
                            'Client-ID' => $clientId,
                            'Authorization' => 'Bearer ' . $token,
                        ])
                        ->withBody('search "Zelda"; fields name; limit 1;', 'text/plain')
                        ->post('https://api.igdb.com/v4/games');

                    if ($igdbResponse->successful() && !empty($igdbResponse->json())) {
                        $gameName = $igdbResponse->json('0.name') ?? 'onbekend';
                        $result = ['success' => true, 'message' => 'Verbinding OK — Token verkregen, test: ' . $gameName];
                    } else {
                        $result = ['success' => false, 'message' => 'IGDB API error: ' . $igdbResponse->status()];
                    }
                    break;

                case 'rebrickable':
                    $apiKey = $credentials['api_key'] ?? null;
                    if (!$apiKey) {
                        $result = ['success' => false, 'message' => 'Geen API key ingesteld'];
                        break;
                    }
                    $response = Http::timeout(10)
                        ->withHeaders(['Authorization' => 'key ' . $apiKey])
                        ->get('https://rebrickable.com/api/v3/lego/sets/', [
                            'search' => 'millennium falcon',
                            'page_size' => 1,
                        ]);
                    if ($response->successful() && !empty($response->json('results'))) {
                        $result = ['success' => true, 'message' => 'Verbinding OK — ' . $response->json('results.0.name')];
                    } else {
                        $result = ['success' => false, 'message' => 'API error: ' . $response->status()];
                    }
                    break;

                case 'brickset':
                    $apiKey = $credentials['api_key'] ?? null;
                    if (!$apiKey) {
                        $result = ['success' => false, 'message' => 'Geen API key ingesteld'];
                        break;
                    }
                    $response = Http::timeout(10)->get('https://brickset.com/api/v3.asmx/checkKey', [
                        'apiKey' => $apiKey,
                    ]);
                    if ($response->successful()) {
                        $result = ['success' => true, 'message' => 'Verbinding OK — API key geldig'];
                    } else {
                        $result = ['success' => false, 'message' => 'API error: ' . $response->status()];
                    }
                    break;
            }
        } catch (\Exception $e) {
            $result = ['success' => false, 'message' => 'Fout: ' . $e->getMessage()];
        }

        return back()->with('test_result_' . $provider->slug, $result);
    }
}
