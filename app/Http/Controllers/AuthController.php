<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();
            $isAdmin = in_array($user->role, ['super_admin', 'direzione', 'segreteria'], true);

            return $isAdmin
                ? redirect()->intended('/admin/dashboard')
                : redirect()->intended('/me/card');
        }

        throw ValidationException::withMessages([
            'email' => 'Le credenziali fornite non sono corrette.',
        ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        abort(404);
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        abort(404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Member->value,
            'membership_status' => 'pending',
        ]);

        Auth::login($user);

        return redirect('/me/card');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
