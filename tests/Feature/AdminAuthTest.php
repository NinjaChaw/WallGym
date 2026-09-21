<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite' || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Auth tests require SQLite in memory.');
        }

        return $app;
    }

    private function admin(): Admin
    {
        return Admin::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'SecurePassword123!']);
    }

    public function test_every_admin_route_is_guarded_and_rejects_guests(): void
    {
        $checked = 0;
        foreach (Route::getRoutes() as $route) {
            if ($route->uri() !== 'admin' && ! str_starts_with($route->uri(), 'admin/')) {
                continue;
            }
            $middleware = $route->gatherMiddleware();
            $this->assertContains('web', $middleware);
            if (in_array($route->getName(), ['admin.login', 'admin.login.store'], true)) {
                $this->assertContains('guest:admin', $middleware);

                continue;
            }
            $this->assertContains('auth:admin', $middleware, $route->uri().' must require admin authentication.');
            $url = '/'.preg_replace('/\{[^}]+\}/', '1', $route->uri());
            foreach (array_diff($route->methods(), ['HEAD']) as $method) {
                $this->call($method, $url)->assertRedirect(route('admin.login'));
                $checked++;
            }
        }
        $this->assertGreaterThan(20, $checked);
    }

    public function test_regular_customer_cannot_perform_admin_mutations(): void
    {
        $this->actingAs(User::factory()->create(), 'web');
        $this->post('/admin/categories', [])->assertRedirect(route('admin.login'));
        $this->patch('/admin/orders/1', [])->assertRedirect(route('admin.login'));
        $this->delete('/admin/products/1')->assertRedirect(route('admin.login'));
        $this->getJson('/admin/products')->assertUnauthorized();
    }

    public function test_admin_forms_require_csrf_tokens(): void
    {
        $this->actingAs($this->admin(), 'admin');
        // Laravel skips CSRF validation during tests unless we change the environment.
        $this->app['env'] = 'local';
        try {
            $this->post('/admin/logout')->assertStatus(419);
            $this->post('/admin/categories', [])->assertStatus(419);
            $this->patch('/admin/orders/1', [])->assertStatus(419);
            $this->delete('/admin/products/1')->assertStatus(419);
        } finally {
            $this->app['env'] = 'testing';
        }
        $this->assertAuthenticated('admin');
    }

    public function test_guests_and_regular_users_cannot_access_admin_routes(): void
    {
        foreach (['/admin', '/admin/categories', '/admin/products', '/admin/orders', '/admin/settings', '/admin/content/faq'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }
        $this->post('/admin/products', [])->assertRedirect(route('admin.login'));
        $this->delete('/admin/products/1')->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create(), 'web')->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk()->assertSee('Welcome back.');
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_login_redirect_logout_and_password_hashing(): void
    {
        $admin = $this->admin();
        $this->assertTrue(Hash::check('SecurePassword123!', $admin->password));
        $this->assertArrayNotHasKey('password', $admin->toArray());
        $this->get('/admin/products')->assertRedirect(route('admin.login'));
        $before = session()->getId();
        $this->post(route('admin.login.store'), ['email' => ' ADMIN@example.test ', 'password' => 'SecurePassword123!'])->assertRedirect(url('/admin/products'));
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertNotSame($before, session()->getId());
        $this->get('/admin/login')->assertRedirect(url('/admin'));
        $this->get('/admin/products')->assertOk();
        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/logout')->assertStatus(405);
    }

    public function test_invalid_login_is_generic_and_rate_limited_without_flashing_password(): void
    {
        $this->admin();
        for ($i = 0; $i < 5; $i++) {
            $this->from(route('admin.login'))->post(route('admin.login.store'), ['email' => 'admin@example.test', 'password' => 'wrong'])->assertSessionHasErrors('email')->assertSessionMissing('_old_input.password');
            $this->assertGuest('admin');
        }
        $this->post(route('admin.login.store'), ['email' => 'admin@example.test', 'password' => 'SecurePassword123!'])->assertSessionHasErrors('email');
        $this->assertGuest('admin');
        $this->travel(61)->seconds();
        $this->post(route('admin.login.store'), ['email' => 'admin@example.test', 'password' => 'SecurePassword123!'])->assertSessionHasNoErrors();
        $this->assertAuthenticated('admin');
    }

    public function test_create_admin_command_has_no_default_password(): void
    {
        $this->artisan('admin:create')->expectsQuestion('Admin name', 'Owner')->expectsQuestion('Admin email', 'owner@example.test')->expectsQuestion('Password (at least 12 characters)', 'SecurePassword123!')->expectsQuestion('Confirm password', 'SecurePassword123!')->expectsOutput('Admin created. Sign in at /admin/login.')->assertExitCode(0);
        $this->assertTrue(Hash::check('SecurePassword123!', Admin::sole()->password));
    }
}
