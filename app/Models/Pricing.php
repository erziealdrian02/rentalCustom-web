<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    /** @use HasFactory<\Database\Factories\PricingFactory> */
    use HasFactory;

    protected $table = 'rental_pricing';

    protected $primaryKey = 'id'; 

    public $incrementing = false;

    protected $keyType = 'string';
}
