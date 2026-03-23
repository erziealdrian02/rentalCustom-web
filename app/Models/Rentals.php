<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rentals extends Model
{
    /** @use HasFactory<\Database\Factories\RentalsFactory> */
    use HasFactory;

    protected $table = 'rentals';
    protected $primaryKey = 'id'; // 🔥 wajib
    public $incrementing = false; // karena UUID
    protected $keyType = 'string';

    protected $fillable = ['invoice_number', 'customer_id', 'warehouse_id', 'delivery_id', 'driver_id', 'rental_start_date', 'rental_end_date', 'estimated_delivery_time', 'actual_delivery_time', 'total_price', 'rental_status', 'payment_status', 'notes', 'created_by'];
}
