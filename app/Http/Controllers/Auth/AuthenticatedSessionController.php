<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();  // 1. Authenticate the user.

        $request->session()->regenerate();  // 2. Regenerate the session ID for security.

        return redirect()->intended(route('home', absolute: false));  // 3. Redirect to intended route or home page.
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();  // 1. Log the user out.

        $request->session()->invalidate();  // 2. Invalidate the current session.

        $request->session()->regenerateToken();  // 3. Regenerate the CSRF token for security.

        // 4. Redirect the user to the home page using named route
        return redirect()->route('home');
    }
}
