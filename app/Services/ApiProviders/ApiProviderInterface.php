<?php

namespace App\Services\ApiProviders;

interface ApiProviderInterface
{
    public function search(string $query, ?string $platform = null): array;
    public function fetchDetails(string $externalId): ?array;
    public function fetchCoverUrl(string $externalId): ?string;
    public function isConfigured(): bool;
    public function getSlug(): string;
}
