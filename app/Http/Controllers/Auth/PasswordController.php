<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UbahPasswordRequest;
use App\Services\PasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Tampilkan form ubah password.
     */
    public function edit(Request $request): View
    {
        return view('profile.change-password', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's password.
     */
    public function update(UbahPasswordRequest $request, PasswordService $passwordService): RedirectResponse
    {
        $passwordService->ubahPassword($request->user(), $request->validated('password'));

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }
}
