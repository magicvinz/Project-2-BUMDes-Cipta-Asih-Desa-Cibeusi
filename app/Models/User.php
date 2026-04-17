<?php

namespace App\Models;

use App\Models\Concerns\ExposesPrimaryKeyAsId;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use ExposesPrimaryKeyAsId;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'User';

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'password',
        'role',
        'id_wisata',
        'google_id',
        'avatar',
        'asal_kota',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'id_wisata', 'id_wisata');
    }

    public function tiket()
    {
        return $this->hasMany(Tiket::class, 'id_user', 'id_user');
    }

    public function isPengunjung(): bool
    {
        return $this->role === 'pengunjung';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPengelolaBumdes(): bool
    {
        return $this->role === 'pengelola_bumdes';
    }
}
