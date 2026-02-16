<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_id'); // Référence classes | sessions de formation
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['exam', 'quiz', 'project', 'practical', 'assignment']);
            
            // Pondération
            $table->decimal('weight', 5, 2)->default(100);
            
            // Notation
            $table->decimal('max_score', 8, 2)->default(20);
            $table->decimal('passing_score', 8, 2);
            
            // Dates
            $table->date('evaluation_date')->nullable();
            $table->boolean('is_mandatory')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('class_id')
                  ->references('id')->on('classes')
                  ->onDelete('cascade');
            
            $table->index('class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};