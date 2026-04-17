<?php

namespace App\Models;

use App\Models\Concerns\ExposesPrimaryKeyAsId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Wisata extends Model
{
    use ExposesPrimaryKeyAsId;
    use HasFactory;

    protected $table = 'Wisata';

    protected $primaryKey = 'id_wisata';

    public function getRouteKeyName(): string
    {
        return 'id_wisata';
    }

    protected $fillable = [
        'nama',
        'slug',
        'harga_tiket',
        'deskripsi',
        'gambar',
        'galleries',
    ];

    protected $casts = [
        'harga_tiket' => 'decimal:0',
        'galleries' => 'array',
    ];

    /** Slug Curug Cibarebeuy (varian penulisan): tiket camping berharga berbeda dari kunjungan. */
    public const SLUGS_CURUG_CIBAREBEUY = ['curug-cibarebeuy', 'curug-cibareubeuy'];

    /** Harga per tiket opsi Camping di Curug; kunjungan memakai kolom `harga_tiket`. */
    public const HARGA_CAMPING_TIKET_CURUG = 25000;

    public function isCurugCibarebeuy(): bool
    {
        return in_array($this->slug, self::SLUGS_CURUG_CIBAREBEUY, true);
    }

    protected static function booted()
    {
        static::creating(function (Wisata $wisata) {
            if (empty($wisata->slug)) {
                $wisata->slug = Str::slug($wisata->nama);
                $base = $wisata->slug;
                $i = 1;
                while (static::where('slug', $wisata->slug)->exists()) {
                    $wisata->slug = $base . '-' . $i++;
                }
            }
        });
        static::updating(function (Wisata $wisata) {
            if ($wisata->isDirty('nama')) {
                $wisata->slug = Str::slug($wisata->nama);
                $base = $wisata->slug;
                $i = 1;
                while (static::where('slug', $wisata->slug)->where($wisata->getKeyName(), '!=', $wisata->getKey())->exists()) {
                    $wisata->slug = $base . '-' . $i++;
                }
            }
        });
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (empty($this->gambar)) {
            return asset('images/wisata-placeholder.svg');
        }
        if (str_starts_with($this->gambar, 'http')) {
            return $this->gambar;
        }
        return Storage::url($this->gambar) ?: asset('images/wisata-placeholder.svg');
    }

    public function tiket()
    {
        return $this->hasMany(Tiket::class, 'id_wisata', 'id_wisata');
    }

    public function penjualanOfflines()
    {
        return $this->hasMany(PenjualanOffline::class, 'id_wisata', 'id_wisata');
    }

    public function admins()
    {
        return $this->hasMany(User::class, 'id_wisata', 'id_wisata');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_wisata', 'id_wisata');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function produkKhas()
    {
        return $this->hasMany(ProdukKhas::class, 'id_wisata', 'id_wisata');
    }
}
