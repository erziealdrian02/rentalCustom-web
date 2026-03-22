<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tools extends Model
{
    /** @use HasFactory<\Database\Factories\ToolsFactory> */
    use HasFactory;

    protected $table = 'tools';
    protected $primaryKey = 'id_tools';
    public $incrementing = false;
    protected $keyType = 'string';

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'tool_id', 'id_tools');
    }
}
