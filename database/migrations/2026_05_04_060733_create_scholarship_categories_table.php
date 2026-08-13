<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scholarship_categories', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
        });
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('category_id');
            $table->string('name', 200);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->string('funding_source', 200)->nullable();
            $table->decimal('monthly_allowance', 10, 2)->nullable();
            $table->enum('benefit_type', ['cash', 'tuition_waiver', 'both', 'other'])->default('cash');
            $table->text('benefit_details')->nullable();
            $table->tinyInteger('allows_concurrent')->default(0);
            $table->unsignedTinyInteger('max_concurrent')->default(0);
            $table->date('application_open_at')->nullable();
            $table->date('application_close_at')->nullable();
            $table->unsignedSmallInteger('slots_available')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('scholarship_categories');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
        Schema::dropIfExists('scholarship_categories');
    }
};
