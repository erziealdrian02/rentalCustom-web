<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tools extends Model
{
    /** @use HasFactory<\Database\Factories\ToolsFactory> */
    use HasFactory;

    protected $table = 'tools';

    protected $primaryKey = 'id_tools'; // 🔥 wajib

    public $incrementing = false; // karena UUID

    protected $keyType = 'string';

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }
}
