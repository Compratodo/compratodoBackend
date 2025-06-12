<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('provider', ['google', 'facebook'])->nullable();
            $table->string('provider_id')->nullable();
            $table->boolean('accepted_terms')->default(false);
            $table->rememberToken();
            $table->timestamps();

            //$table->check('accepted_terms = 1');
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};

