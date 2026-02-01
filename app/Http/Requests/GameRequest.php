<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'platform' => 'nullable|string|max:100',
            'release_date' => 'nullable|date',
            'genre' => 'nullable|string|max:255',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|in:collection,wishlist',
            'completion_status' => 'required|in:not_played,playing,completed,platinum',
            'rating' => 'nullable|integer|min:1|max:10',
            'format' => 'required|in:physical,digital,both',
            'barcode' => 'nullable|string|max:50',
            'cover_image_url' => 'nullable|url|max:500',
            'external_api_id' => 'nullable|string|max:255',
            'external_api_source' => 'nullable|string|max:50',
        ];
    }
}
