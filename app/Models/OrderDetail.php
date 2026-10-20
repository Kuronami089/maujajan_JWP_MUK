<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model untuk rincian item dalam pesanan
class OrderDetail extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'order_details';

    // Kolom 'id' dijaga agar tidak bisa diisi manual, kolom lainnya diizinkan
    protected $guarded = ['id'];

    // Relasi: Setiap rincian terhubung ke 1 data menu makanan
    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }

    // Relasi: Setiap rincian dimiliki oleh 1 pesanan utama
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
