<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';
    protected $primaryKey = 'id'; // 🔥 wajib
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'vehicle_type', 'license_plate', 'phone', 'email','status'];
}
