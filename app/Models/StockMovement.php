<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';
    protected $primaryKey = 'id'; // 🔥 wajib
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['warehouse_id', 'tool_id', 'movement_type', 'stock_type', 'quantity', 'notes', 'created_by', 'reference_id'];
}
