<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Type de formation
            $table->uuid('formation_type_id');
            
            $table->string('code')->unique(); // Ex: FRM-2024-001
            $table->string('name');
            $table->text('description')->nullable();
            
            // Mode de formation
            $table->enum('mode', ['online', 'presentiel', 'hybrid'])->default('online');
            
            // Durée
            $table->integer('duration_hours')->nullable();
            $table->integer('duration_days')->nullable();
            
            // Tarification
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('XOF'); // Franc CFA pour Bénin
            
            // Capacité
            $table->integer('max_students')->nullable();
            
            // Prérequis
            $table->json('prerequisites')->nullable();
            
            // Statut
            $table->boolean('is_active')->default(true);
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('formation_type_id')
                  ->references('id')->on('formation_types')
                  ->onDelete('restrict');
            
            $table->index('code');
            $table->index('formation_type_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};