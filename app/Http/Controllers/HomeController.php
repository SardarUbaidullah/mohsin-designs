<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * HomeController constructor.
     * Apply guest middleware so authenticated users don't see the login form.
     */


    /**
     * Show the custom login view (your UI).
     *
     * Make sure your blade with the form is e.g. resources/views/welcome.blade.php
     * or change the view name below to whatever file contains your form.
     */
    public function index()
    {
        // If already logged in, redirect to dashboard (or wherever you want)
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }

        // return the blade that contains your custom UI form
        return view('home'); // <-- change if your file is different
    }

    /**
     * Handle login form POST from "/".
     */
    public function login(Request $request)
    {
        // Validate input (keeps your UI intact)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session to prevent fixation
            $request->session()->regenerate();

            // Redirect to intended destination (dashboard by default)
            return redirect()->intended('/dashboard');
        }

        // Authentication failed: send back to the same page with error and old email
        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    /**
     * Optional: logout helper if you want to place logout on this controller.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
