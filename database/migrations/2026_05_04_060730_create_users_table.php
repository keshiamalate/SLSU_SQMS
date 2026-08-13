<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('role_id');
            $table->string('institutional_id', 30)->unique();
            $table->string('email', 191)->unique();
            $table->string('password', 255);
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix', 20)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->string('mfa_secret', 255)->nullable();
            $table->tinyInteger('mfa_enabled')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('users');
    }
};
