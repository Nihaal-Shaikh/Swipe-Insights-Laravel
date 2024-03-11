<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public function status()
    {
        return $this->belongsTo(ImageStatus::class, 'image_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
