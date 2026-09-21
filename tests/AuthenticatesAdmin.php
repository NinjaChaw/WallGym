<?php

namespace Tests;

use App\Models\Admin;

trait AuthenticatesAdmin
{
    protected function setUpAuthenticatesAdmin(): void
    {
        $this->actingAs(Admin::create(['name' => 'Test Admin', 'email' => 'admin@example.test', 'password' => 'TestPassword123!']), 'admin');
    }
}
