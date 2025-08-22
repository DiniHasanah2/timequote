<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PFlavourMap;

class PFlavourMapController extends Controller
{
    public function index(Request $request)
    {
        

    $highlight = $request->query('highlight'); // e.g. ?highlight=c3.large
        $flavours = PFlavourMap::orderBy('created_at', 'asc')->get();

        return view('flavour.index', compact('flavours','highlight'));
    }
}
