<?php

namespace App\Models;

use App\Models\Concerns\ExposesPrimaryKeyAsId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProdukKhas extends Model
{
    use ExposesPrimaryKeyAsId;
    use HasFactory;

    protected $table = 'Produk_Khas';

    protected $primaryKey = 'id_produk_khas';

    public function getRouteKeyName(): string
    {
        return 'id_produk_khas';
    }

    protected $fillable = [
        'nama',
        'keterangan',
        'gambar',
        'urutan',
        'galleries',
        'id_wisata',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'galleries' => 'array',
    ];

    /**
     * URL gambar: jika path lokal, pakai storage; jika sudah URL penuh, pakai langsung.
     */
    public function getGambarUrlAttribute(): ?string
    {
        if (empty($this->gambar)) {
            return asset('images/produk-placeholder.svg');
        }
        if (str_starts_with($this->gambar, 'http')) {
            return $this->gambar;
        }
        return Storage::url($this->gambar) ?: asset('images/produk-placeholder.svg');
    }

    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'id_wisata', 'id_wisata');
    }
}
