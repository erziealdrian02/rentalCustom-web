<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    /** @use HasFactory<\Database\Factories\CategoriesFactory> */
    use HasFactory;

    protected $table = 'tool_categories';

    protected $primaryKey = 'id'; // 🔥 wajib

    public $incrementing = false; // karena UUID

    protected $keyType = 'string';

    protected $fillable = ['name', 'description'];

    public function tools()
    {
        return $this->hasMany(Tools::class, 'category_id');
    }
}
