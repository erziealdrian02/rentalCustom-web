<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function masterCategories(Request $request)
    {

        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $categories = Categories::paginate($perPage);

        return view('master.categories', compact('categories'));
    }

    public function masterCategoriesStore(Request $request)
    {
        $model = new Categories();
        $model->name = $request->name;
        $model->description = $request->description;

        $model->save();

        return redirect()->route('master.categories')->with('success', 'Category added successfully!');
    }

    public function masterCategoriesUpdate(Request $request, $id)
    {
        $category = Categories::findOrFail($id);
        $category->name = $request->name;
        $category->description = $request->description;

        $category->save();

        return redirect()->route('master.categories')->with('success', 'Category updated successfully!');
    }

    public function masterCategoriesDestroy($id)
    {
        $category = Categories::findOrFail($id);
        $category->delete();

        return redirect()->route('master.categories')->with('success', 'Category deleted successfully!');
    }
}
