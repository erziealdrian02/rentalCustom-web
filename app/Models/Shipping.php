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

    protected $fillable = ['id', 'delivery_number', 'driver_id', 'rental_id', 'from_location', 'to_location', 'departure_time', 'estimated_arrival_time', 'actual_arrival_time', 'delivery_status', 'proof_image_url', 'notes'];
}
