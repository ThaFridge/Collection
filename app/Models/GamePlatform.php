<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamePlatform extends Model
{
    protected $fillable = [
        'game_id', 'platform', 'format', 'status', 'completion_status',
        'purchase_price', 'purchase_date', 'condition', 'barcode', 'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function scopeCollection($query)
    {
        return $query->where('status', 'collection');
    }

    public function scopeWishlist($query)
    {
        return $query->where('status', 'wishlist');
    }
}
