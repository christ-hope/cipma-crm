<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('formation_id')->unique();
            
            // Critères
            $table->decimal('note_minimale', 5, 2)->default(10.00);
            $table->decimal('presence_minimale', 5, 2)->default(80.00);
            
            // Paiement
            $table->boolean('paiement_complet_requis')->default(true);
            $table->decimal('paiement_minimum_pourcentage', 5, 2)->nullable();
            
            // Examens
            $table->boolean('examens_obligatoires')->default(true);
            $table->json('examens_requis')->nullable();
            
            // Validation manuelle
            $table->boolean('validation_manuelle_responsable')->default(false);
            
            // Règles personnalisées
            $table->json('regles_personnalisees')->nullable();
            
            $table->timestamps();
            
            $table->foreign('formation_id')
                  ->references('id')->on('formations')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_rules');
    }
};