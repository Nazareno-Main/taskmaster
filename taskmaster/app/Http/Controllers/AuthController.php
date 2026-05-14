<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * AuthController — Handles student authentication
 * Methods: showLogin, login, showRegister, register, logout
 */
class AuthController extends Controller
{
    /** Show the login form */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the student.
     * Validates credentials, then creates a session on success.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // Prevent session fixation
            return redirect()->route('tasks.index');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /** Show the registration form */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Register a new student account.
     * Validates input, hashes password, creates user, logs in automatically.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect()->route('tasks.index')
            ->with('success', 'Welcome to TaskMaster, ' . $user->name . '!');
    }

    /**
     * Log the student out and invalidate the session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
