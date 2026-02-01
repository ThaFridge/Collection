<?php

namespace App\DTOs;

class LegoSearchResult
{
    public function __construct(
        public string $name,
        public ?string $setNumber = null,
        public ?string $imageUrl = null,
        public ?int $releaseYear = null,
        public ?int $pieceCount = null,
        public ?int $minifigureCount = null,
        public ?string $theme = null,
        public ?string $subtheme = null,
        public ?string $externalId = null,
        public ?string $source = null,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'set_number' => $this->setNumber,
            'image_url' => $this->imageUrl,
            'release_year' => $this->releaseYear,
            'piece_count' => $this->pieceCount,
            'minifigure_count' => $this->minifigureCount,
            'theme' => $this->theme,
            'subtheme' => $this->subtheme,
            'external_id' => $this->externalId,
            'source' => $this->source,
        ];
    }
}
