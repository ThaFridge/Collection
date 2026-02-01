<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::creating(function (Tag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_tag');
    }

    public function legoSets()
    {
        return $this->belongsToMany(LegoSet::class, 'lego_set_tag');
    }
}
