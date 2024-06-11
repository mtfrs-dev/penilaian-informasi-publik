<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respondent_score', function (Blueprint $table) {
            $table->id();
            // Respondent ID
            $table->foreignId('respondent_id')->references('id')->on('users')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            // Jury ID
            $table->unsignedBigInteger('jury_id')->nullable();
            $table->foreign('jury_id')->references('id')->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            // Status 'is done'
            $table->boolean('is_done')->default(false);
            // Total Score
            $table->float('total_score')->default(0);
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('respondent_score');
    }
};
