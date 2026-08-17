<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_audit_log_page(): void
    {
        $roleSuperAdmin = Role::create(['nama' => 'Super Admin', 'kode' => 'super_admin']);
        $user = User::factory()->create([
            'role_id' => $roleSuperAdmin->id,
            'status' => true,
        ]);

        AuditLog::catat('Test Activity', 'Testing audit log activity rendering');

        $response = $this->actingAs($user)->get(route('super-admin.audit-log'));

        $response->assertStatus(200);
        $response->assertSee('Audit Log');
        $response->assertSee('Test Activity');
    }
}
