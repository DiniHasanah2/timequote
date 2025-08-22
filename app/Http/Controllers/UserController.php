<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        
        return view('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
    'password' => [
        'required',
        'string',
        'min:12',
        'regex:/[a-z]/',      // small letter
        'regex:/[A-Z]/',      // big letter
        'regex:/[0-9]/',      // number
        'regex:/[@$!%*#?&]/', // symbol
    ],
]);

    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
