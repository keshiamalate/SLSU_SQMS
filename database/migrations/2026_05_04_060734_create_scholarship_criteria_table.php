<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scholarship_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarship_id');
            $table->decimal('min_gpa', 4, 2)->nullable();
            $table->decimal('max_gpa', 4, 2)->nullable();
            $table->tinyInteger('no_failing_grade')->default(0);
            $table->json('required_year_levels')->nullable();
            $table->json('required_courses')->nullable();
            $table->string('required_honors', 100)->nullable();
            $table->decimal('max_annual_income', 12, 2)->nullable();
            $table->tinyInteger('requires_4ps')->default(0);
            $table->json('required_income_brackets')->nullable();
            $table->tinyInteger('requires_slsu_residency')->default(0);
            $table->json('required_municipalities')->nullable();
            $table->tinyInteger('requires_athlete')->default(0);
            $table->tinyInteger('requires_student_leader')->default(0);
            $table->tinyInteger('requires_pwd')->default(0);
            $table->tinyInteger('requires_indigenous_people')->default(0);
            $table->tinyInteger('requires_philippine_citizenship')->default(1);
            $table->tinyInteger('requires_active_enrollment')->default(1);
            $table->text('additional_requirements')->nullable();
            $table->timestamps();
            $table->foreign('scholarship_id')->references('id')->on('scholarships')->onDelete('cascade');
            $table->unique('scholarship_id');
        });
        Schema::create('scholarship_required_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarship_id');
            $table->string('document_name', 200);
            $table->text('description')->nullable();
            $table->tinyInteger('is_mandatory')->default(1);
            $table->unsignedTinyInteger('display_order')->default(0);
            $table->foreign('scholarship_id')->references('id')->on('scholarships')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('scholarship_required_documents');
        Schema::dropIfExists('scholarship_criteria');
    }
};
