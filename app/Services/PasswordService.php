<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordService
{
    /**
     * Ubah password user dan catat ke Audit Log.
     */
    public function ubahPassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($newPassword),
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        AuditLog::catat(
            aktivitas: 'Ubah Password',
            deskripsi: 'Mengubah password akun sendiri',
            modelType: User::class,
            modelId: $user->id
        );
    }
}
