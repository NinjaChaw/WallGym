<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Create an admin account with an interactively entered password';

    public function handle(): int
    {
        $data = [
            'name' => trim((string) $this->ask('Admin name')),
            'email' => strtolower(trim((string) $this->ask('Admin email'))),
            'password' => $this->secret('Password (at least 12 characters)'),
            'password_confirmation' => $this->secret('Confirm password'),
        ];
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:admins,email'],
            'password' => ['required', 'string', 'max:72', 'confirmed', Password::min(12)->letters()->numbers()],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }
        Admin::create(collect($validator->validated())->except('password_confirmation')->all());
        $this->info('Admin created. Sign in at /admin/login.');

        return self::SUCCESS;
    }
}
