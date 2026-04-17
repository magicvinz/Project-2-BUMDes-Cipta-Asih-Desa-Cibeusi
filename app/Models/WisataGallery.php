<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WisataGallery extends Model
{
    use HasFactory;
    protected $fillable = ['id_wisata', 'image', 'caption'];

    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'id_wisata', 'id_wisata');
    }
}
