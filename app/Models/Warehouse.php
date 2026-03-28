<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /** @use HasFactory<\Database\Factories\WarehouseFactory> */
    use HasFactory;

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'warehouse_id', 'id');
    }

    public function movement_stocks()
    {
        return $this->hasMany(StockMovement::class, 'warehouse_id', 'id');
    }
}
