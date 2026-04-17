<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKhasGallery extends Model
{
    use HasFactory;
    protected $fillable = ['id_produk_khas', 'image', 'caption'];

    public function produkKhas()
    {
        return $this->belongsTo(ProdukKhas::class, 'id_produk_khas', 'id_produk_khas');
    }
}
