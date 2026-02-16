<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Informations personnelles
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('birth_date');
            $table->string('birth_place')->nullable();
            $table->string('nationality');
            $table->text('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country');
            
            // Parcours académique
            $table->string('last_diploma')->nullable();
            $table->string('institution')->nullable();
            $table->integer('graduation_year')->nullable();
            $table->text('academic_background')->nullable();
            
            // Formations demandées
            $table->json('requested_formations');
            
            // Statut
            $table->enum('status', [
                'submitted',
                'under_review',
                'approved',
                'rejected',
                'info_requested'
            ])->default('submitted');
            
            // Déclaration légale
            $table->boolean('legal_declaration')->default(false);
            $table->timestamp('declaration_date')->nullable();
            
            // Workflow
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Relation vers Student
            $table->uuid('student_id')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('reviewed_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();
            
            // IMPORTANT: nullOnDelete car student peut être supprimé après
            $table->foreign('student_id')
                  ->references('id')->on('students')
                  ->nullOnDelete();
            
            $table->index('email');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};