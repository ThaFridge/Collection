<?php

namespace App\DTOs;

class GameSearchResult
{
    public function __construct(
        public string $name,
        public ?string $platform = null,
        public ?string $coverUrl = null,
        public ?string $releaseDate = null,
        public ?string $genre = null,
        public ?string $developer = null,
        public ?string $publisher = null,
        public ?string $description = null,
        public ?string $externalId = null,
        public ?string $source = null,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'platform' => $this->platform,
            'cover_url' => $this->coverUrl,
            'release_date' => $this->releaseDate,
            'genre' => $this->genre,
            'developer' => $this->developer,
            'publisher' => $this->publisher,
            'description' => $this->description,
            'external_id' => $this->externalId,
            'source' => $this->source,
        ];
    }
}
