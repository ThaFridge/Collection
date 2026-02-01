<?php

namespace App\Services\ApiProviders;

interface LegoProviderInterface
{
    public function search(string $query): array;
    public function fetchDetails(string $externalId): ?array;
    public function isConfigured(): bool;
    public function getSlug(): string;
}
