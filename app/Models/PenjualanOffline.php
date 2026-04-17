<?php

namespace App\Models;

use App\Models\Concerns\ExposesPrimaryKeyAsId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanOffline extends Model
{
    use ExposesPrimaryKeyAsId;
    use HasFactory;

    protected $table = 'Penjualan_Offline';

    protected $primaryKey = 'id_penjualan_offline';

    public function getRouteKeyName(): string
    {
        return 'id_penjualan_offline';
    }

    protected $fillable = [
        'id_wisata',
        'tanggal',
        'jumlah_tiket',
        'total_pendapatan',
        'id_user',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'id_wisata', 'id_wisata');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
