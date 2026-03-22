<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    /** @use HasFactory<\Database\Factories\CustomersFactory> */
    use HasFactory;

    protected $table = 'customers';

    protected $primaryKey = 'id'; // 🔥 wajib

    public $incrementing = false; // karena UUID

    protected $keyType = 'string';
}
