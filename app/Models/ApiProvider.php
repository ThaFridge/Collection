<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiProvider extends Model
{
    protected $fillable = ['slug', 'name', 'is_active', 'priority', 'credentials_json'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
            'credentials_json' => 'array',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderByDesc('priority');
    }
}
