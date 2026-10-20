<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Model untuk data pengguna / admin
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Kolom yang boleh diisi saat registrasi atau pembuatan user
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // Kolom yang disembunyikan saat data user dikonversi ke array/JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Konversi tipe data otomatis (misal: password otomatis di-hash)
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
