<?php

namespace App\Services;

use App\Models\ApiProvider;
use App\Services\ApiProviders\LegoProviderInterface;
use App\Services\ApiProviders\RebrickableProvider;

class LegoSearchService
{
    private array $providerMap = [
        'rebrickable' => RebrickableProvider::class,
    ];

    public function search(string $query): array
    {
        $activeProviders = ApiProvider::active()->get();
        $results = [];

        foreach ($activeProviders as $provider) {
            if (!isset($this->providerMap[$provider->slug])) continue;

            $instance = app($this->providerMap[$provider->slug]);

            if (!$instance->isConfigured()) continue;

            $providerResults = $instance->search($query);
            $results = array_merge($results, $providerResults);
        }

        return $results;
    }
}
