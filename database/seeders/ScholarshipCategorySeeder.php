<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScholarshipCategory;

class ScholarshipCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Government-Funded', 'description' => 'CHED UniFAST, PESFA, TWSP, LGU grants'],
            ['name' => 'Institution-Based', 'description' => 'SLSU academic excellence and leadership grants'],
            ['name' => 'Private/Corporate', 'description' => 'SM Foundation, Ayala Foundation, etc.'],
            ['name' => 'Special Category', 'description' => 'Athletes, student leaders, PWD, IP scholars'],
            ['name' => 'Need-Based', 'description' => '4Ps beneficiaries and indigent-family grants'],
        ];
        foreach ($categories as $cat) {
            ScholarshipCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
