<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model untuk data pesanan utama
class Order extends Model
{
    use HasFactory;

    // Kolom 'id' dijaga agar tidak bisa diisi manual, kolom lainnya diizinkan
    protected $guarded = ['id'];

    // Relasi: 1 pesanan bisa memiliki banyak rincian menu
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
