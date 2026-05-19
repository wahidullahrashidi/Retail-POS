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
                return redirect()->route('pos.dashboard');
            }

            return redirect()->route('pos.dashboard');
        }

        return back()->with('error', 'Invalid username or password');
    }

    public function pinLogin(Request $request)
    {
        $request->validate([
            'pin_code' => 'required|string|size:4',
        ]);

        $pin = $request->pin_code;

        $user = User::where('is_active', true)
            ->get()
            ->first(function (User $user) use ($pin) {
                if (! $user->pin_code) {
                    return false;
                }

                return Hash::check($pin, $user->pin_code) || hash_equals($user->pin_code, $pin);
            });

        if ($user) {
            if (! Hash::check($pin, $user->pin_code)) {
                $user->forceFill(['pin_code' => Hash::make($pin)])->save();
            }

            Auth::login($user);
            $user->update(['last_login_at' => now()]);

            if ($user->role->name === 'cashier') {
                return redirect()->route('pos.dashboard');
            }

            return redirect()->route('pos.dashboard');
        }

        return back()->with('error', 'Invalid PIN code');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
