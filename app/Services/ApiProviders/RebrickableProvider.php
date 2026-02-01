<?php

namespace App\Services\ApiProviders;

use App\DTOs\LegoSearchResult;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

class RebrickableProvider implements LegoProviderInterface
{
    private ?string $apiKey;

    public function __construct()
    {
        $provider = ApiProvider::where('slug', 'rebrickable')->first();
        $credentials = is_array($provider?->credentials_json) ? $provider->credentials_json : [];
        $this->apiKey = $credentials['api_key'] ?? null;
    }

    public function getSlug(): string
    {
        return 'rebrickable';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function search(string $query): array
    {
        if (!$this->isConfigured()) return [];

        $response = Http::timeout(10)
            ->withHeaders(['Authorization' => 'key ' . $this->apiKey])
            ->get('https://rebrickable.com/api/v3/lego/sets/', [
                'search' => $query,
                'page_size' => 10,
                'ordering' => '-year',
            ]);

        if (!$response->successful()) return [];

        return collect($response->json('results', []))->map(function ($set) {
            $setNumber = str_replace('-1', '', $set['set_num'] ?? '');

            return new LegoSearchResult(
                name: $set['name'] ?? '',
                setNumber: $setNumber,
                imageUrl: $set['set_img_url'] ?? null,
                releaseYear: $set['year'] ?? null,
                pieceCount: $set['num_parts'] ?? null,
                theme: null,
                externalId: $set['set_num'] ?? '',
                source: 'rebrickable',
            );
        })->toArray();
    }

    public function fetchDetails(string $externalId): ?array
    {
        if (!$this->isConfigured()) return null;

        $response = Http::timeout(10)
            ->withHeaders(['Authorization' => 'key ' . $this->apiKey])
            ->get("https://rebrickable.com/api/v3/lego/sets/{$externalId}/");

        if (!$response->successful()) return null;

        $set = $response->json();
        $setNumber = str_replace('-1', '', $set['set_num'] ?? '');

        // Try to get theme info
        $theme = null;
        if (!empty($set['theme_id'])) {
            $themeResponse = Http::timeout(10)
                ->withHeaders(['Authorization' => 'key ' . $this->apiKey])
                ->get("https://rebrickable.com/api/v3/lego/themes/{$set['theme_id']}/");
            if ($themeResponse->successful()) {
                $theme = $themeResponse->json('name');
            }
        }

        // Try to get minifigure count
        $minifigCount = null;
        $minifigResponse = Http::timeout(10)
            ->withHeaders(['Authorization' => 'key ' . $this->apiKey])
            ->get("https://rebrickable.com/api/v3/lego/sets/{$externalId}/minifigs/");
        if ($minifigResponse->successful()) {
            $minifigCount = $minifigResponse->json('count', 0);
        }

        return [
            'name' => $set['name'] ?? '',
            'set_number' => $setNumber,
            'image_url' => $set['set_img_url'] ?? null,
            'release_year' => $set['year'] ?? null,
            'piece_count' => $set['num_parts'] ?? null,
            'minifigure_count' => $minifigCount,
            'theme' => $theme,
        ];
    }
}
