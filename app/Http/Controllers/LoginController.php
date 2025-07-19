<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Uses Laravel's default auth.login view
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Attempt to authenticate the user
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
    // Authentication successful
    $request->session()->regenerate();

    $user = Auth::user();

    // Redirect based on user role (is_admin)
    if ($user->is_admin) {
        return redirect()->intended('/admin/dashboard')->with('success', 'Welcome back, Admin!');
    }

    return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
}


        // Authentication failed
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register'); // Uses Laravel's default auth.register view
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'user_type' => 'required|in:student,staff',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'phone' => $request->phone,
        ]);

        // Log the user in
        Auth::login($user);

        // Redirect to dashboard after successful registration
        return redirect('/dashboard')->with('success', 'Registration successful! Welcome to UNZA Carbon Calculator.');
    }
}
