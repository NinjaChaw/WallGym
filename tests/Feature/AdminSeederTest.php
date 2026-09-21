<?php

namespace Tests\Feature;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Seeder tests require SQLite in memory.');
        }

        return $app;
    }

    public function test_seeder_creates_working_login_and_preserves_existing_credentials(): void
    {
        config(['admin.seed' => ['name' => 'Owner', 'email' => ' OWNER@example.test ', 'password' => 'SecurePassword123!']]);
        $this->seed(AdminSeeder::class);
        $admin = Admin::sole();
        $this->assertSame('owner@example.test', $admin->email);
        $this->assertTrue(Hash::check('SecurePassword123!', $admin->password));
        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'SecurePassword123!'])->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($admin, 'admin');
        config(['admin.seed.name' => 'Changed', 'admin.seed.password' => 'AnotherPassword123!']);
        $this->seed(AdminSeeder::class);
        $this->assertDatabaseCount('admins', 1);
        $this->assertSame('Owner', $admin->fresh()->name);
        $this->assertSame($admin->password, $admin->fresh()->password);
    }

    public function test_missing_password_cannot_create_default_credentials(): void
    {
        config(['admin.seed' => ['name' => 'Owner', 'email' => 'owner@example.test', 'password' => null]]);
        try {
            $this->seed(AdminSeeder::class);
            $this->fail('Expected validation failure.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('password', $exception->errors());
        }
        $this->assertDatabaseCount('admins', 0);
    }
}
