<?php

namespace App\Services\ApiProviders;

use App\DTOs\GameSearchResult;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;

class RawgProvider implements ApiProviderInterface
{
    private ?string $apiKey;

    public function __construct()
    {
        $provider = ApiProvider::where('slug', 'rawg')->first();
        $credentials = $provider?->credentials_json;
        $this->apiKey = is_array($credentials) ? ($credentials['api_key'] ?? null) : null;
    }

    public function getSlug(): string
    {
        return 'rawg';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function search(string $query, ?string $platform = null): array
    {
        if (!$this->isConfigured()) return [];

        $params = ['key' => $this->apiKey, 'search' => $query, 'page_size' => 10];

        $response = Http::get('https://api.rawg.io/api/games', $params);

        if (!$response->successful()) return [];

        return collect($response->json('results', []))->map(function ($game) {
            return new GameSearchResult(
                name: $game['name'] ?? '',
                coverUrl: $game['background_image'] ?? null,
                releaseDate: $game['released'] ?? null,
                genre: collect($game['genres'] ?? [])->pluck('name')->implode(', ') ?: null,
                description: null,
                externalId: (string) ($game['id'] ?? ''),
                source: 'rawg',
            );
        })->toArray();
    }

    public function fetchDetails(string $externalId): ?array
    {
        if (!$this->isConfigured()) return null;

        $response = Http::get(https://api.rawg.io/api/games/{$externalId}, ['key' => $this->apiKey]);

        if (!$response->successful()) return null;

        $game = $response->json();
        return [
            'name' => $game['name'] ?? '',
            'description' => strip_tags($game['description'] ?? ''),
            'release_date' => $game['released'] ?? null,
            'genre' => collect($game['genres'] ?? [])->pluck('name')->implode(', '),
            'developer' => collect($game['developers'] ?? [])->pluck('name')->implode(', '),
            'publisher' => collect($game['publishers'] ?? [])->pluck('name')->implode(', '),
            'cover_url' => $game['background_image'] ?? null,
        ];
    }

    public function fetchCoverUrl(string $externalId): ?string
    {
        $details = $this->fetchDetails($externalId);
        return $details['cover_url'] ?? null;
    }

    public function fetchAchievements(string $externalId): ?array
    {
        if (!$this->isConfigured()) return null;

        $achievements = [];
        $page = 1;

        do {
            $response = Http::get("https://api.rawg.io/api/games/{$externalId}/achievements", [
                'key' => $this->apiKey,
                'page_size' => 50,
                'page' => $page,
            ]);

            if (!$response->successful()) break;

            $data = $response->json();
            $results = $data['results'] ?? [];

            foreach ($results as $a) {
                $achievements[] = [
                    'name' => $a['name'] ?? '',
                    'description' => $a['description'] ?? null,
                    'image_url' => $a['image'] ?? null,
                    'percent' => isset($a['percent']) ? (float) $a['percent'] : null,
                ];
            }

            $page++;
        } while (!empty($data['next']) && $page <= 10);

        return $achievements;
    }

    public function fetchScreenshots(string $externalId, int $max = 8): array
    {
        if (!$this->isConfigured()) return [];

        $response = Http::get("https://api.rawg.io/api/games/{$externalId}/screenshots", [
            'key' => $this->apiKey,
            'page_size' => $max,
        ]);

        if (!$response->successful()) return [];

        return collect($response->json('results', []))
            ->take($max)
            ->pluck('image')
            ->filter()
            ->values()
            ->toArray();
    }
}
