<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('course', 150);
            $table->unsignedTinyInteger('year_level');
            $table->unsignedTinyInteger('semester');
            $table->decimal('cumulative_gpa', 4, 2);
            $table->tinyInteger('has_failing_grade')->default(0);
            $table->string('academic_honors', 100)->nullable();
            $table->enum('enrollment_status', ['regular', 'irregular', 'transferee'])->default('regular');
            $table->text('annual_family_income_enc');
            $table->unsignedTinyInteger('number_of_dependents')->default(0);
            $table->tinyInteger('is_4ps_beneficiary')->default(0);
            $table->enum('income_bracket', ['A', 'B', 'C', 'D', 'E']);
            $table->string('province_of_residence', 100);
            $table->string('municipality_of_residence', 100);
            $table->tinyInteger('is_slsu_resident')->default(0);
            $table->tinyInteger('is_athlete')->default(0);
            $table->tinyInteger('is_student_leader')->default(0);
            $table->tinyInteger('is_pwd')->default(0);
            $table->tinyInteger('is_indigenous_people')->default(0);
            $table->timestamp('profile_completed_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
