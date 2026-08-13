<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('scholarship_id');
            $table->decimal('academic_score', 5, 4)->nullable();
            $table->decimal('financial_score', 5, 4)->nullable();
            $table->decimal('course_score', 5, 4)->nullable();
            $table->decimal('year_level_score', 5, 4)->nullable();
            $table->decimal('special_qual_score', 5, 4)->nullable();
            $table->decimal('weighted_score', 5, 4)->nullable();
            $table->decimal('ml_probability', 5, 4)->nullable();
            $table->decimal('final_score', 5, 4)->nullable();
            $table->enum('match_label', ['top_match', 'good_match', 'possible_match'])->nullable();
            $table->enum('status', ['matched', 'applied', 'under_review', 'documents_pending', 'approved', 'rejected', 'withdrawn'])->default('matched');
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->text('decision_notes')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('scholarship_id')->references('id')->on('scholarships');
            $table->foreign('reviewed_by')->references('id')->on('users');
            $table->unique(['user_id', 'scholarship_id']);
        });
        Schema::create('document_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('user_id');
            $table->string('original_filename', 255);
            $table->string('stored_filename', 255);
            $table->string('storage_path', 500);
            $table->unsignedInteger('file_size_bytes');
            $table->string('mime_type', 100);
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('document_uploads');
        Schema::dropIfExists('applications');
    }
};
