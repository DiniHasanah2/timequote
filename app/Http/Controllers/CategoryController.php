<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    public function index()
{
    //$categories = Category::all();
    $categories = Category::orderBy('created_at', 'asc')->get();
    return view('products/category/index', compact('categories'));
}

public function store(Request $request)
{
    if (!in_array(auth()->user()->role, ['admin', 'product'])) {
    abort(403, 'Unauthorized action.');
}

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255|unique:categories,category_code',
    ]);

    Category::create([
        'name' => $validated['name'],
        'category_code' => $validated['code'],
    ]);

    return redirect()->route('categories.index')->with('success', 'Category created successfully.');
}
public function edit($id)
{
    if (!in_array(auth()->user()->role, ['admin', 'product'])) {
    abort(403, 'Unauthorized action.');
}

    $category = Category::findOrFail($id);
    return view('products.category.edit', compact('category'));
}

public function update(Request $request, $id)
{
    if (!in_array(auth()->user()->role, ['admin', 'product'])) {
    abort(403, 'Unauthorized action.');
}

    $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255',
    ]);

    $category = Category::findOrFail($id);
    $category->name = $request->name;
    $category->category_code = $request->code;
    $category->save();

    return redirect()->route('categories.edit', $category->id)
        ->with('success', 'Category updated successfully.');
}


public function destroy(Category $category)
{
    if (!in_array(auth()->user()->role, ['admin', 'product'])) {
    abort(403, 'Unauthorized action.');
}

    $category->delete();

    return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
}


/*public function show($id)
{
    return redirect()->route('categories.index')->with('success', 'Export successfully!'); 
}*/



public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = fopen($request->file('csv_file'), 'r');
    $header = fgetcsv($file); // skip header

    while (($row = fgetcsv($file)) !== false) {
        Category::create([
            'name' => $row[0],
            'category_code' => $row[1],
          

        ]);
    }

    fclose($file);

    return redirect()->back()->with('success', 'Category imported successfully.');
}

public function export()
{
    $categories = \App\Models\Category::all();

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="categories_export.csv"',
    ];

    $callback = function () use ($categories) {
        $handle = fopen('php://output', 'w');

        // Header baris pertama
        fputcsv($handle, ['ID', 'Name', 'Code']);

        // Data baris seterusnya
        foreach ($categories as $category) {
            fputcsv($handle, [
                $category->id,
                $category->name,
                $category->category_code,
                //$category->created_at,
            ]);
        }

        fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
}



}

