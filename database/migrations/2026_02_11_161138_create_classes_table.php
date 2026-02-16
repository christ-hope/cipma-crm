<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('formation_id');
            
            $table->string('code')->unique(); // Ex: FRM-2024-001-C01
            $table->string('name');
            
            // Dates
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // Lieu
            $table->string('location')->nullable();
            $table->text('location_details')->nullable();
            
            // Formateur
            $table->uuid('instructor_id')->nullable();
            
            // Capacité
            $table->integer('max_students')->nullable();
            $table->integer('enrolled_count')->default(0);
            
            // Statut
            $table->enum('status', [
                'planned',
                'registration_open',
                'registration_closed',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('planned');
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('formation_id')
                  ->references('id')->on('formations')
                  ->onDelete('cascade');
            
            $table->foreign('instructor_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
            
            $table->index('formation_id');
            $table->index('status');
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};