<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Magazine extends Model
{
    protected $fillable = [
        'type', 'title', 'publisher', 'issue_number',
        'publication_date', 'year',
        'cover_image_path', 'pdf_path', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'year' => 'integer',
        ];
    }
}
