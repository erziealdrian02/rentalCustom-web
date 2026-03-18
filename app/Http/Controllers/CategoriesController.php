<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    private function getCategories()
    {
        return session('categories', [['id' => 1, 'name' => 'Power Tools', 'description' => 'Electric and battery-powered tools'], ['id' => 2, 'name' => 'Hand Tools', 'description' => 'Manual tools operated by hand'], ['id' => 3, 'name' => 'Safety Equipment', 'description' => 'Personal protective equipment'], ['id' => 4, 'name' => 'Measurement Tools', 'description' => 'Tools for measuring and marking'], ['id' => 5, 'name' => 'Cleaning Equipment', 'description' => 'Tools and equipment for cleaning']]);
    }

    public function masterCategories()
    {
        $categories = $this->getCategories();
        return view('master.categories', compact('categories'));
    }
}
