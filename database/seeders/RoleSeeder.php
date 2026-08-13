<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'description' => 'Full system access'],
            ['name' => 'scholarship_admin', 'description' => 'Manages scholarships and applications'],
            ['name' => 'verifier', 'description' => 'Reviews and verifies documents'],
            ['name' => 'student', 'description' => 'Completes profile and views matches'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
