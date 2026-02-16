<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->string('name'); // Ex: "Formation Moodle", "Formation Interne"
            $table->string('slug')->unique(); // Ex: "moodle", "internal"
            $table->text('description')->nullable();
            
            // Configuration
            $table->boolean('requires_certification')->default(true);
            $table->enum('evaluation_mode', ['crm', 'external', 'manual'])->default('crm');
            
            // Statut
            $table->boolean('is_active')->default(true);
            
            // Traçabilité
            $table->uuid('created_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();
            
            $table->index('slug');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_types');
    }
};