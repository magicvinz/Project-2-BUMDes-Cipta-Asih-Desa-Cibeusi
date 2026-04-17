<?php

namespace App\Models;

use App\Models\Concerns\ExposesPrimaryKeyAsId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use ExposesPrimaryKeyAsId;
    use HasFactory;

    protected $table = 'Ulasan';

    protected $primaryKey = 'id_ulasan';

    public function getRouteKeyName(): string
    {
        return 'id_ulasan';
    }

    protected $fillable = [
        'id_user',
        'id_wisata',
        'id_tiket',
        'rating',
        'comment',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'id_wisata', 'id_wisata');
    }

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'id_tiket', 'id_tiket');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto)) {
            return null;
        }
        if (str_starts_with($this->foto, 'http')) {
            return $this->foto;
        }
        return \Illuminate\Support\Facades\Storage::url($this->foto);
    }
}
