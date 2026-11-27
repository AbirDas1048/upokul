<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Lock-out recovery from the server's command line: the admin panel has no "forgot password" e-mail
 * (a broken mail setup would make that useless exactly when it is needed).
 */
class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password
                            {email? : Login e-mail of the admin (default: the master administrator)}
                            {--password= : New password (omit it to be asked; avoid typing it on the command line)}';

    protected $description = 'Set a new password for an admin user (and re-activate the account)';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = $email
            ? User::query()->where('email', $email)->first()
            : User::query()->where('is_master', true)->orderBy('id')->first();

        if (! $user) {
            $this->error($email ? "No admin user with e-mail {$email}." : 'No master administrator found. Has the installation wizard been completed?');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret("New password for {$user->email}");

        if ($password !== null && ! $this->option('password') && $password !== $this->secret('Repeat the password')) {
            $this->error('The two passwords do not match.');

            return self::FAILURE;
        }

        $validator = Validator::make(['password' => $password], ['password' => ['required', Password::min(10)->letters()->mixedCase()->numbers()]]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first('password'));

            return self::FAILURE;
        }

        $user->password = $password;
        $user->is_active = true;
        $user->save();

        $this->info("Password updated for {$user->email}. You can sign in at ".url('/admin/login').'.');

        return self::SUCCESS;
    }
}
