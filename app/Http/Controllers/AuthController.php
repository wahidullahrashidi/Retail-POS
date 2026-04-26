<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)
            ->where('is_active', true)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $user->update(['last_login_at' => now()]);

            if ($user->role->name === 'cashier') {
                return redirect()->route('pos.index');
            }

            return redirect()->route('pos.index');
        }

        return back()->with('error', 'Invalid username or password');
    }

    public function pinLogin(Request $request)
    {
        $request->validate([
            'pin_code' => 'required|string|size:4',
        ]);

        $user = User::where('pin_code', $request->pin_code)
            ->where('is_active', true)
            ->first();

        if ($user) {
            Auth::login($user);
            $user->update(['last_login_at' => now()]);

            if ($user->role->name === 'cashier') {
                return redirect()->route('pos.index');
            }

            return redirect()->route('pos.index');
        }

        return back()->with('error', 'Invalid PIN code');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
