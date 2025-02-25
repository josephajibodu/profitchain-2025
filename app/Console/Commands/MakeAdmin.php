<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-admin {email} {--super : Assign the super admin role instead of admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user an admin or super admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $isSuperAdmin = $this->option('super');

        // Find the user by email
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Determine the role to assign
        $roleName = $isSuperAdmin ? 'Super Admin' : 'Admin';
        $role = Role::query()->where('name', $roleName)->first();

        if (!$role) {
            $this->error("Role '{$roleName}' not found. Please create this role.");
            return 1;
        }

        // Assign the role to the user
        $user->assignRole($role);

        $roleLabel = $isSuperAdmin ? 'Super Admin' : 'Admin';
        $this->info("User '{$user->username}' ({$user->email}) is now a {$roleLabel}.");
        return 0;
    }
}