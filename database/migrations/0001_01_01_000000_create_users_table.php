<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('name');
            $table->string('email')->unique();

            // 👉 Thêm dòng này để fix lỗi seed
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            // Thêm đúng như file SQL
            $table->enum('role', ['user', 'expert', 'admin'])
                  ->default('user');
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();

            // 👉 Thêm dòng này để hỗ trợ "remember me"
            $table->rememberToken();

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
