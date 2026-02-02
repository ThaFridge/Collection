<?php

namespace App\Services\ApiProviders;

use App\DTOs\GameSearchResult;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class IgdbProvider implements ApiProviderInterface
{
    private ?string $clientId;
    private ?string $clientSecret;

    public function __construct()
    {
        $provider = ApiProvider::where('slug', 'igdb')->first();
        $credentials = is_array($provider?->credentials_json) ? $provider->credentials_json : [];
        $this->clientId = $credentials['client_id'] ?? null;
        $this->clientSecret = $credentials['client_secret'] ?? null;
    }

    public function getSlug(): string
    {
        return 'igdb';
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    public function search(string $query, ?string $platform = null): array
    {
        if (!$this->isConfigured()) return [];

        $token = $this->getAccessToken();
        if (!$token) return [];

        $body = 'search "' . addslashes($query) . '"; fields name,cover.url,first_release_date,genres.name,involved_companies.company.name,involved_companies.developer,involved_companies.publisher,summary; limit 10;';

        $response = Http::timeout(10)
            ->withHeaders([
                'Client-ID' => $this->clientId,
                'Authorization' => 'Bearer ' . $token,
            ])
            ->withBody($body, 'text/plain')
            ->post('https://api.igdb.com/v4/games');

        if (!$response->successful()) return [];

        return collect($response->json())->map(function ($game) {
            $coverUrl = null;
            if (!empty($game['cover']['url'])) {
                $coverUrl = str_replace('t_thumb', 't_cover_big', $game['cover']['url']);
                if (str_starts_with($coverUrl, '//')) {
                    $coverUrl = 'https:' . $coverUrl;
                }
            }

            $developers = [];
            $publishers = [];
            foreach ($game['involved_companies'] ?? [] as $ic) {
                $companyName = $ic['company']['name'] ?? null;
                if (!$companyName) continue;
                if (!empty($ic['developer'])) $developers[] = $companyName;
                if (!empty($ic['publisher'])) $publishers[] = $companyName;
            }

            $releaseDate = null;
            if (!empty($game['first_release_date'])) {
                $releaseDate = date('Y-m-d', $game['first_release_date']);
            }

            return new GameSearchResult(
                name: $game['name'] ?? '',
                coverUrl: $coverUrl,
                releaseDate: $releaseDate,
                genre: collect($game['genres'] ?? [])->pluck('name')->implode(', ') ?: null,
                developer: implode(', ', $developers) ?: null,
                publisher: implode(', ', $publishers) ?: null,
                description: $game['summary'] ?? null,
                externalId: (string) ($game['id'] ?? ''),
                source: 'igdb',
            );
        })->toArray();
    }

    public function fetchDetails(string $externalId): ?array
    {
        if (!$this->isConfigured()) return null;

        $token = $this->getAccessToken();
        if (!$token) return null;

        $body = 'where id = ' . (int) $externalId . '; fields name,cover.url,first_release_date,genres.name,involved_companies.company.name,involved_companies.developer,involved_companies.publisher,summary;';

        $response = Http::timeout(10)
            ->withHeaders([
                'Client-ID' => $this->clientId,
                'Authorization' => 'Bearer ' . $token,
            ])
            ->withBody($body, 'text/plain')
            ->post('https://api.igdb.com/v4/games');

        if (!$response->successful() || empty($response->json())) return null;

        $game = $response->json()[0];

        $coverUrl = null;
        if (!empty($game['cover']['url'])) {
            $coverUrl = 'https:' . str_replace('t_thumb', 't_cover_big', $game['cover']['url']);
        }

        return [
            'name' => $game['name'] ?? '',
            'description' => $game['summary'] ?? null,
            'release_date' => !empty($game['first_release_date']) ? date('Y-m-d', $game['first_release_date']) : null,
            'genre' => collect($game['genres'] ?? [])->pluck('name')->implode(', '),
            'cover_url' => $coverUrl,
        ];
    }

    public function fetchCoverUrl(string $externalId): ?string
    {
        $details = $this->fetchDetails($externalId);
        return $details['cover_url'] ?? null;
    }

    public function fetchScreenshots(string $externalId, int $max = 8): array
    {
        if (!$this->isConfigured()) return [];

        $token = $this->getAccessToken();
        if (!$token) return [];

        $body = 'where game = ' . (int) $externalId . '; fields url; limit ' . $max . ';';

        $response = Http::timeout(10)
            ->withHeaders([
                'Client-ID' => $this->clientId,
                'Authorization' => 'Bearer ' . $token,
            ])
            ->withBody($body, 'text/plain')
            ->post('https://api.igdb.com/v4/screenshots');

        if (!$response->successful()) return [];

        return collect($response->json())
            ->pluck('url')
            ->filter()
            ->map(function ($url) {
                $url = str_replace('t_thumb', 't_screenshot_big', $url);
                return str_starts_with($url, '//') ? 'https:' . $url : $url;
            })
            ->take($max)
            ->values()
            ->toArray();
    }

    public function fetchAchievements(string $externalId): ?array
    {
        // IGDB v4 does not have an achievements endpoint
        return null;
    }

    private function getAccessToken(): ?string
    {
        return Cache::remember('igdb_access_token', 3600, function () {
            $response = Http::timeout(10)->post('https://id.twitch.tv/oauth2/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if (!$response->successful()) return null;

            return $response->json('access_token');
        });
    }
}
