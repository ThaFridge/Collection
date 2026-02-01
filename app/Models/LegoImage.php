<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegoImage extends Model
{
    public $timestamps = false;
    protected $fillable = ['lego_set_id', 'image_path', 'type', 'sort_order'];

    public function legoSet()
    {
        return $this->belongsTo(LegoSet::class);
    }
}
