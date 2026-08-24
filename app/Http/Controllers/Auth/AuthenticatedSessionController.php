<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\AuditLog;
use App\Models\User;

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
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        AuditLog::catat('Login', "User login: {$user->nama}", User::class, $user->id);
        $dashboardRoute = match($user->getRoleKode()) {
            'super_admin' => 'super-admin.dashboard',
            'admin_kabupaten' => 'kabupaten.dashboard',
            'admin_opd' => 'opd.dashboard',
            'admin_kecamatan' => 'kecamatan.dashboard',
            'admin_desa' => 'desa.dashboard',
            'admin_dpmd' => 'dpmd.dashboard',
            default => 'home',
        };

        return redirect()->intended(route($dashboardRoute));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
            AuditLog::catat('Logout', "User logout: {$user->nama}", User::class, $user->id);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
