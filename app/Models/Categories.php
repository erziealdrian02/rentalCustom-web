<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    /** @use HasFactory<\Database\Factories\CategoriesFactory> */
    protected $table = 'tool_categories';

    use HasFactory;

    public function tools()
    {
        return $this->hasMany(Tools::class, 'category_id');
    }
}
