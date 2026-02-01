<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Game extends Model
{
    protected $fillable = [
        'name', 'platform', 'slug', 'cover_image_path', 'cover_image_url',
        'release_date', 'genre', 'developer', 'publisher', 'description',
        'purchase_price', 'purchase_date', 'condition', 'notes', 'status',
        'completion_status', 'rating', 'format', 'barcode',
        'external_api_id', 'external_api_source', 'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'metadata_json' => 'array',
            'rating' => 'integer',
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

    public function scopeCollection(Builder $query): Builder
    {
        return $query->where('status', 'collection');
    }

    public function scopeWishlist(Builder $query): Builder
    {
        return $query->where('status', 'wishlist');
    }

    public function images()
    {
        return $this->hasMany(GameImage::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'game_tag');
    }
}
