<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LegoSet extends Model
{
    protected $table = 'lego_sets';

    protected $fillable = [
        'set_number', 'name', 'slug', 'theme', 'subtheme',
        'piece_count', 'minifigure_count', 'image_path', 'image_url',
        'release_year', 'retail_price', 'purchase_price', 'purchase_date',
        'condition', 'status', 'build_status', 'notes',
        'instructions_url', 'bricklink_url',
        'external_api_id', 'external_api_source', 'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'retail_price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'piece_count' => 'integer',
            'minifigure_count' => 'integer',
            'release_year' => 'integer',
            'metadata_json' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LegoSet $set) {
            if (empty($set->slug)) {
                $set->slug = Str::slug($set->name . '-' . $set->set_number);
            }
            if (empty($set->instructions_url) && $set->set_number) {
                $set->instructions_url = 'https://www.lego.com/nl-nl/service/buildinginstructions/' . $set->set_number;
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

    public function scopeBuilt(Builder $query): Builder
    {
        return $query->whereIn('build_status', ['built', 'displayed']);
    }

    public function scopeNotBuilt(Builder $query): Builder
    {
        return $query->whereIn('build_status', ['not_built', 'in_progress']);
    }

    public function images()
    {
        return $this->hasMany(LegoImage::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'lego_set_tag');
    }
}
