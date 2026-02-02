<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Game extends Model
{
    protected $fillable = [
        'name', 'slug', 'cover_image_path', 'cover_image_url',
        'release_date', 'genre', 'developer', 'publisher', 'description',
        'notes', 'rating',
        'external_api_id', 'external_api_source', 'metadata_json',
        'achievements_fetched', 'achievements_supported',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'metadata_json' => 'array',
            'rating' => 'integer',
            'achievements_fetched' => 'boolean',
            'achievements_supported' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Game $game) {
            if (empty($game->slug)) {
                $game->slug = Str::slug($game->name);
            }
        });
    }

    public function platforms()
    {
        return $this->hasMany(GamePlatform::class);
    }

    public function images()
    {
        return $this->hasMany(GameImage::class)->orderBy('sort_order');
    }

    public function achievements()
    {
        return $this->hasMany(GameAchievement::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'game_tag');
    }

    /**
     * Games that have at least one platform in collection.
     */
    public function scopeInCollection(Builder $query): Builder
    {
        return $query->whereHas('platforms', fn ($q) => $q->where('status', 'collection'));
    }

    /**
     * Games that have at least one platform on wishlist.
     */
    public function scopeOnWishlist(Builder $query): Builder
    {
        return $query->whereHas('platforms', fn ($q) => $q->where('status', 'wishlist'));
    }
}
