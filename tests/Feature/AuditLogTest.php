<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_logins_and_failed_logins_are_audited(): void
    {
        // A voter account: back-office accounts are only logged in after the second factor
        $user = User::factory()->create(['username' => 'authority']);

        $this->post('/auth/login', ['username' => 'authority', 'password' => 'wrong-password']);
        $this->post('/auth/login', ['username' => 'authority', 'password' => 'password']);

        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.failed', 'user_id' => $user->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.login', 'user_id' => $user->id]);
        $this->assertStringNotContainsString('wrong-password', AuditLog::all()->toJson());
    }

    public function test_audit_entries_cannot_be_modified_or_deleted(): void
    {
        $log = AuditLog::create(['action' => 'test']);

        $this->expectException(LogicException::class);
        $log->update(['action' => 'tampered']);
    }

    public function test_audit_entries_cannot_be_deleted(): void
    {
        $log = AuditLog::create(['action' => 'test']);

        $this->expectException(LogicException::class);
        $log->delete();
    }
}
