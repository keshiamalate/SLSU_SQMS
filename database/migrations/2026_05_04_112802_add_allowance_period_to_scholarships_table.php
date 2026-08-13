<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->enum('allowance_period', ['monthly', 'per_semester', 'per_year', 'one_time'])
                ->default('monthly')
                ->after('monthly_allowance');
        });
    }
    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn('allowance_period');
        });
    }
};
