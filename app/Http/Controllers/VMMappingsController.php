<?php

namespace App\Http\Controllers;

use App\Models\VMMappings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class VMMappingsController extends Controller
{
    public function index()
    {
       
        Artisan::call('vm:sync');

        // Now load data from vm_mappings table
        $vmMappings = VMMappings::all();
        

        return view('vm_mapping.index', compact('vmMappings'));
    }
}
