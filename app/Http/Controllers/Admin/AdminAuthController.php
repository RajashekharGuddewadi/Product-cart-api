<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->withErrors(['general' => 'Invalid email or password']);
        }

        if (!$user->role || $user->role->name !== 'admin') {
            return back()->withErrors(['general' => 'Admin access only']);
        }

        Auth::guard('web')->login($user);

        return redirect()->route('admin.products.list')->with('success', 'Admin login successful!');
    }
}
