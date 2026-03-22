<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
    use HasFactory;

    protected $table = 'warehouse_stock';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function tool()
    {
        return $this->belongsTo(Tools::class, 'tool_id', 'id_tools');
    }

}
