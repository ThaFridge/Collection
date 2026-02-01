<?php

namespace App\Services;

use App\Models\ApiProvider;
use App\Services\ApiProviders\ApiProviderInterface;
use App\Services\ApiProviders\RawgProvider;
use App\Services\ApiProviders\IgdbProvider;

class GameSearchService
{
    private array $providerMap = [
        'rawg' => RawgProvider::class,
        'igdb' => IgdbProvider::class,
    ];

    public function search(string $query, ?string $platform = null): array
    {
        $activeProviders = ApiProvider::active()->get();
        $results = [];

        foreach ($activeProviders as $provider) {
            if (!isset($this->providerMap[$provider->slug])) continue;

            $instance = app($this->providerMap[$provider->slug]);

            if (!$instance->isConfigured()) continue;

            $providerResults = $instance->search($query, $platform);
            $results = array_merge($results, $providerResults);
        }

        return $results;
    }
}
