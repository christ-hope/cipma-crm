<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('class_id'); // Référence classes | sessions de formation
            
            // Statut
            $table->enum('status', [
                'pending',
                'active',
                'completed',
                'withdrawn',
                'failed'
            ])->default('pending');
            
            // Dates
            $table->timestamp('enrolled_at');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Suivi
            $table->decimal('attendance_rate', 5, 2)->nullable();
            $table->integer('sessions_attended')->default(0);
            $table->integer('sessions_total')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('student_id')
                  ->references('id')->on('students')
                  ->onDelete('cascade');
            
            $table->foreign('class_id')
                  ->references('id')->on('classes')
                  ->onDelete('cascade');
            
            $table->unique(['student_id', 'class_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};