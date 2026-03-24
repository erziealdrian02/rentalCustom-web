<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    /** @use HasFactory<\Database\Factories\ShippingFactory> */
    use HasFactory;

    protected $table = 'shippings';
    protected $primaryKey = 'id'; // 🔥 wajib
    public $incrementing = false; // karena UUID
    protected $keyType = 'string';
}
