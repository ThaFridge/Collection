<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameAchievement extends Model
{
    protected $fillable = [
        'game_id', 'name', 'description', 'image_url', 'percent',
    ];

    protected $casts = [
        'percent' => 'decimal:2',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
