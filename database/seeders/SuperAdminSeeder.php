<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'super_admin')->first();
        User::firstOrCreate(
            ['institutional_id' => 'ADMIN-0001'],
            [
                'role_id' => $role->id,
                'email' => 'superadmin@slsu.edu.ph',
                'password' => Hash::make('SmartMatch@2025!'),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'is_active' => 1,
                'mfa_enabled' => 0,
            ]
        );
    }
}
