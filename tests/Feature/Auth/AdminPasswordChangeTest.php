<?php

namespace Tests\Feature\Auth;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(string $roleKode): User
    {
        $role = Role::firstOrCreate(
            ['kode' => $roleKode],
            ['nama' => ucfirst(str_replace('_', ' ', $roleKode)), 'deskripsi' => 'Role ' . $roleKode]
        );

        return User::factory()->create([
            'role_id' => $role->id,
            'password' => Hash::make('OldPassword123!'),
            'status' => true,
        ]);
    }

    public function test_all_admin_roles_can_view_change_password_page(): void
    {
        $roles = ['super_admin', 'admin_kabupaten', 'admin_opd', 'admin_kecamatan', 'admin_desa'];

        foreach ($roles as $roleKode) {
            $user = $this->createAdminUser($roleKode);

            $response = $this->actingAs($user)->get(route('password.change'));

            $response->assertStatus(200);
            $response->assertSee('Ubah Password');
        }
    }

    public function test_unauthenticated_user_cannot_access_change_password_page(): void
    {
        $response = $this->get(route('password.change'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_successfully_change_password(): void
    {
        $user = $this->createAdminUser('super_admin');

        $response = $this->actingAs($user)
            ->from(route('password.change'))
            ->put(route('password.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ]);

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHas('success', 'Password berhasil diubah.');

        $this->assertTrue(Hash::check('NewPassword456!', $user->refresh()->password));
        $this->assertFalse(Hash::check('OldPassword123!', $user->password));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'aktivitas' => 'Ubah Password',
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
    }

    public function test_incorrect_old_password_is_rejected(): void
    {
        $user = $this->createAdminUser('admin_kabupaten');

        $response = $this->actingAs($user)
            ->from(route('password.change'))
            ->put(route('password.update'), [
                'current_password' => 'WrongPassword123!',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ]);

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHasErrors(['current_password']);

        $this->assertTrue(Hash::check('OldPassword123!', $user->refresh()->password));
    }

    public function test_mismatched_password_confirmation_is_rejected(): void
    {
        $user = $this->createAdminUser('admin_opd');

        $response = $this->actingAs($user)
            ->from(route('password.change'))
            ->put(route('password.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'DifferentPassword789!',
            ]);

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHasErrors(['password']);

        $this->assertTrue(Hash::check('OldPassword123!', $user->refresh()->password));
    }

    public function test_same_new_and_old_password_is_rejected(): void
    {
        $user = $this->createAdminUser('admin_kecamatan');

        $response = $this->actingAs($user)
            ->from(route('password.change'))
            ->put(route('password.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'OldPassword123!',
                'password_confirmation' => 'OldPassword123!',
            ]);

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHasErrors(['password']);

        $this->assertTrue(Hash::check('OldPassword123!', $user->refresh()->password));
    }

    public function test_password_is_stored_hashed_never_plaintext(): void
    {
        $user = $this->createAdminUser('admin_desa');

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'SecurePass999!',
                'password_confirmation' => 'SecurePass999!',
            ]);

        $user->refresh();

        $this->assertNotEquals('SecurePass999!', $user->password);
        $this->assertTrue(Hash::check('SecurePass999!', $user->password));
    }
}
