<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model untuk data menu makanan & minuman
class Food extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'food';

    // Kolom 'id' dijaga agar tidak bisa diisi manual, kolom lainnya diizinkan
    protected $guarded = ['id'];
}
