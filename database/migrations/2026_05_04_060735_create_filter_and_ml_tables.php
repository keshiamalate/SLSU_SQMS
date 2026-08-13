<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_existing_scholarships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('scholarship_name', 200);
            $table->string('scholarship_type', 100)->nullable();
            $table->string('granting_body', 200)->nullable();
            $table->tinyInteger('is_exclusive')->default(0);
            $table->timestamp('declared_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::create('duplicate_filter_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('scholarship_id');
            $table->string('conflict_source', 200);
            $table->enum('filter_result', ['blocked', 'allowed']);
            $table->unsignedBigInteger('override_by')->nullable();
            $table->text('override_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('scholarship_id')->references('id')->on('scholarships');
            $table->foreign('override_by')->references('id')->on('users');
        });
        Schema::create('ml_models', function (Blueprint $table) {
            $table->id();
            $table->string('model_name', 100);
            $table->string('version', 20);
            $table->decimal('accuracy', 5, 4);
            $table->decimal('f1_score', 5, 4);
            $table->decimal('precision_score', 5, 4);
            $table->decimal('recall_score', 5, 4);
            $table->unsignedInteger('training_records');
            $table->json('feature_names');
            $table->string('storage_path', 500);
            $table->tinyInteger('is_active')->default(0);
            $table->timestamp('trained_at');
            $table->timestamp('deployed_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ml_models');
        Schema::dropIfExists('duplicate_filter_log');
        Schema::dropIfExists('student_existing_scholarships');
    }
};
