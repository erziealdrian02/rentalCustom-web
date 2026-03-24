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

    public function tool()
    {
        return $this->belongsTo(Tools::class, 'tool_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}
