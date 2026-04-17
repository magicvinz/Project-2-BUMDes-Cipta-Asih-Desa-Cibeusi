<?php

namespace App\Models;

use App\Models\Concerns\ExposesPrimaryKeyAsId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use ExposesPrimaryKeyAsId;
    use HasFactory;

    protected $table = 'Tiket';

    protected $primaryKey = 'id_tiket';

    public function getRouteKeyName(): string
    {
        return 'id_tiket';
    }

    protected $fillable = [
        'id_user',
        'id_wisata',
        'kode_tiket',
        'jumlah',
        'total_harga',
        'status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'tanggal_berkunjung',
        'camping',
        'parkir_tipe',
        'parkir_harga',
        'used_at',
    ];

    protected $casts = [
        'total_harga' => 'decimal:0',
        'parkir_harga' => 'decimal:0',
        'tanggal_berkunjung' => 'date',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'id_wisata', 'id_wisata');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    /**
     * Keterangan parkir untuk ditampilkan di tiket (dibayar di lokasi).
     */
    public function getParkirKeteranganAttribute(): ?string
    {
        if (empty($this->parkir_tipe)) {
            return null;
        }
        $labels = [
            'motor_kunjungan' => 'Motor Kunjungan - Rp ' . number_format(config('parkir.motor_kunjungan', 10000), 0, ',', '.') . ' (include penitipan helm & barang)',
            'mobil_kunjungan' => 'Mobil Kunjungan - Rp ' . number_format(config('parkir.mobil_kunjungan', 15000), 0, ',', '.'),
            'motor_camping' => 'Motor Camping - Rp ' . number_format(config('parkir.motor_camping', 15000), 0, ',', '.') . ' (include penitipan helm & barang)',
            'mobil_camping' => 'Mobil Camping - Rp ' . number_format(config('parkir.mobil_camping', 25000), 0, ',', '.'),
        ];
        $label = $labels[$this->parkir_tipe] ?? ucfirst(str_replace('_', ' ', $this->parkir_tipe));
        return $label . ' — dibayar di lokasi';
    }

    /**
     * Isi teks yang di-encode ke QR (berbeda per wisata).
     */
    public function getQrContentAttribute(): string
    {
        if (!$this->relationLoaded('wisata')) {
            $this->load('wisata');
        }
        
        $lines = [
            $this->kode_tiket,
            (string) $this->jumlah,
            \Carbon\Carbon::parse($this->tanggal_berkunjung)->format('Y-m-d'),
        ];
        
        if ($this->wisata && $this->wisata->isCurugCibarebeuy() && $this->camping) {
            $lines[] = $this->camping === 'Ya' ? 'Camping' : 'Kunjungan';
        }
        
        return implode("\n", $lines);
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'id_tiket', 'id_tiket');
    }
}
