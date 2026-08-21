<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreUserRequest;
use App\Http\Requests\Master\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Role;
use App\Models\Opd;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $query = User::with(['role', 'opd', 'kecamatan', 'desa'])
                    ->search(['nama', 'username'], request('search'));

        if (request()->filled('role_id')) {
            $query->where('role_id', request('role_id'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status') === 'aktif' ? 1 : 0);
        }

        if (request('urutan_waktu') === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } elseif (request('urutan_waktu') === 'terbaru') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->sort(request('sort', 'created_at'), request('dir', 'desc'));
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();
        $opdList = Opd::orderBy('nama_opd')->get();
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();
        return view('super-admin.users.index', compact('users', 'roles', 'opdList', 'kecamatanList'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'opd_id' => $validated['opd_id'] ?? null,
            'kecamatan_id' => $validated['kecamatan_id'] ?? null,
            'desa_id' => $validated['desa_id'] ?? null,
        ]);

        AuditLog::catat('Tambah User', "User: {$user->nama}", User::class, $user->id, null, ['nama' => $user->nama, 'username' => $user->username, 'role_id' => $user->role_id]);
        return redirect()->route('super-admin.master.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $dataLama = $user->only(['nama', 'username', 'role_id', 'opd_id', 'kecamatan_id', 'desa_id']);
        $user->update($request->only(['nama', 'username', 'role_id', 'opd_id', 'kecamatan_id', 'desa_id']));

        $passwordChanged = false;
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
            $passwordChanged = true;
        }

        $dataBaru = $user->only(['nama', 'username', 'role_id', 'opd_id', 'kecamatan_id', 'desa_id']);
        if ($passwordChanged) $dataBaru['password'] = '[DIUBAH]';
        AuditLog::catat('Ubah User', "User: {$user->nama}", User::class, $user->id, $dataLama, $dataBaru);
        return redirect()->route('super-admin.master.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        AuditLog::catat('Hapus User', "User: {$user->nama}", User::class, $user->id, $user->only(['nama', 'username', 'role_id']), null);
        $user->delete();
        return redirect()->route('super-admin.master.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $oldStatus = $user->status;
        $user->status = !$user->status;
        $user->save();
        AuditLog::catat('Toggle Status User', "User: {$user->nama} → " . ($user->status ? 'Aktif' : 'Nonaktif'), User::class, $user->id, ['status' => $oldStatus], ['status' => $user->status]);
        return redirect()->back()->with('success', 'Status user berhasil diubah.');
    }
}
