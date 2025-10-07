<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\Service;
use Illuminate\Support\Str; 
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
    
     //$products = Product::with(['customer', 'service'])->orderBy('created_at', 'asc')->get();
     $products = Product::with(['service', 'quotation.version'])->orderBy('created_at', 'asc')->get();


        $customers = Customer::all();
        $quotations = Quotation::with('project.customer', 'version')->get(); // make sure ada relation
        $services = Service::all();

      
        $availableQuotations = $quotations;

    return view('products.index', compact('products', 'customers', 'quotations', 'services',  'availableQuotations' ));

}

public function store(Request $request)
{
    //dd(auth()->id());
    $validated = $request->validate([
        //'division' => 'required|string',
        //'department' => 'required|string',
        //'customer_id' => 'required|exists:customers,id',
        'quotation_id' => 'required|exists:quotations,id',
        'services_id' => 'required|exists:services,id',
        'quantity' => 'required|numeric|min:0',
        'priceperunit' => 'required|numeric|min:0',
        'totalprice' => 'required|numeric|min:0',
    ]);

    $product = new Product();
    $product->id = (string) Str::uuid();
    //$product->customer_id = $validated['customer_id'];
    $product->quotation_id = $validated['quotation_id'];
    $product->services_id = $validated['services_id'];
    $product->quantity = $validated['quantity'];
    $product->priceperunit = $validated['priceperunit'];
    $product->totalprice = $validated['totalprice'];
    $product->presale_id = auth()->user()->id; 
    $product->save();

    return redirect()->route('products.index')->with('success', 'Product added successfully.');
}


public function edit($id)
{
    $product = Product::with('service', 'customer')->findOrFail($id);
    $services = Service::all();
    return view('products.edit', compact('product', 'services'));
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'services_id' => 'required|exists:services,id',
        'quantity' => 'required|numeric|min:0',
        'priceperunit' => 'required|numeric|min:0',
        'totalprice' => 'required|numeric|min:0',
    ]);

    $product = Product::findOrFail($id);
    $product->services_id = $validated['services_id'];
    $product->quantity = $validated['quantity'];
    $product->priceperunit = $validated['priceperunit'];
    $product->totalprice = $validated['totalprice'];
    $product->save();

    return redirect()->route('products.index')->with('success', 'Product updated successfully.');
}



public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = fopen($request->file('csv_file'), 'r');
    $header = fgetcsv($file); // skip header

    while (($row = fgetcsv($file)) !== false) {
        $customer = Customer::where('name', $row[0])->first();
        $quotation = Quotation::where('name', $row[1])->first();

        if (!$customer || !$quotation) {
            continue;
        }

        Product::create([
            'id' => (string) Str::uuid(),
            'customer_id' => $customer->id,
            'quotation_id' => $quotation->id,
            'service_code' => $row[2],
            'quantity' => $row[3],
            'price_per_unit' => $row[4],
            'total_price' => $row[5],
        ]);
    }

    fclose($file);

    return redirect()->back()->with('success', 'Products imported successfully.');
}





}
