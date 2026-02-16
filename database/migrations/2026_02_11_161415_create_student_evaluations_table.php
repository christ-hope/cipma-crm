<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('class_id'); // Référence classes
            $table->uuid('evaluation_id')->nullable();
            
            // Source de l'évaluation
            $table->enum('source', ['crm', 'moodle', 'manual'])->default('crm');
            
            // Notes
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('note_finale', 8, 2);
            
            // Validation
            $table->enum('statut_validation', [
                'en_cours',
                'valide',
                'non_valide',
                'en_attente'
            ])->default('en_cours');
            
            // Présence
            $table->boolean('presence')->nullable();
            
            // Commentaires
            $table->text('commentaire')->nullable();
            
            // Traçabilité
            $table->uuid('saisi_par')->nullable();
            $table->timestamp('saisi_le')->nullable();
            $table->uuid('valide_par')->nullable();
            $table->timestamp('valide_le')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('student_id')
                  ->references('id')->on('students')
                  ->onDelete('cascade');
            
            $table->foreign('class_id')
                  ->references('id')->on('classes')
                  ->onDelete('cascade');
            
            $table->foreign('evaluation_id')
                  ->references('id')->on('evaluations')
                  ->nullOnDelete();
            
            $table->foreign('saisi_par')
                  ->references('id')->on('users')
                  ->nullOnDelete();
            
            $table->foreign('valide_par')
                  ->references('id')->on('users')
                  ->nullOnDelete();
            
            $table->index(['student_id', 'class_id']);
            $table->index('source');
            $table->index('statut_validation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_evaluations');
    }
};