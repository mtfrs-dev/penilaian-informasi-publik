<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_verification_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token');
            $table->dateTime('expiry_time');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('email_verification_tokens');
    }
};