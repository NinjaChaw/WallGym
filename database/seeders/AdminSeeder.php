<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $data = config('admin.seed');
        
        $data['email'] = strtolower(trim((string) ($data['email'] ?? '')));
        Validator::make($data, ['email' => ['required', 'email', 'max:150']])->validate();

        if (Admin::where('email', $data['email'])->exists()) {
            $this->command?->info('Admin already exists; credentials were left unchanged.');

            return;
        }

        $validated = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'max:72', Password::min(8)->letters()->numbers()],
        ])->validate();

        // Admin's hashed cast securely stores the password.
        Admin::firstOrCreate(['email' => $validated['email']], $validated);
        $this->command?->info('Admin account seeded. Sign in at /admin/login.');
    }
}
