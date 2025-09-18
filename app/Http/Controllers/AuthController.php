<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function showRegisterForm()
{
    return view('auth.register'); 
}


    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'required|email|max:255|unique:users,email',
        'role' => 'required|in:admin,presale,product',
        'password' => [
            'required',
            'string',
            'min:12',
            'regex:/[a-z]/',        
            'regex:/[A-Z]/',        
            'regex:/[0-9]/',       
            'regex:/[@$!%*#?&]/',   
            'confirmed',
        ],
    ]);

    $user = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'role' => $request->role ?? 'presale',
        'password' => bcrypt($request->password),
    ]);

    Auth::login($user);

    
    if ($user->role === 'admin') {
        return redirect()->intended(route('admin.dashboard'));
    }

    if (in_array($user->role, ['presale', 'product'])) {
        return redirect()->intended(route('presale.dashboard'));
    }

    return redirect()->route('login');
}


    

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        \Log::info('Login attempt', ['username' => $request->username]);

    if (Auth::attempt($credentials, $request->remember)) {
        $request->session()->regenerate();
        $user = Auth::user();
        
        \Log::info('Login successful', [
            'user_id' => $user->id,
            'role' => $user->role,
            'session_id' => session()->getId()
        ]);

     

    if ($user->role === 'admin') {
    return redirect()->intended(route('admin.dashboard'));
}

if (in_array($user->role, ['presale', 'product'])) {
    return redirect()->intended(route('presale.dashboard'));
}

return redirect()->route('login'); 


    }
    
    \Log::warning('Login failed', ['username' => $request->username]);
    return back()->withErrors(['username' => 'Credentials invalid']);
}
        public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    
}
