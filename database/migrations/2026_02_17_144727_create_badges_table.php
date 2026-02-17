<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('student_id');
            $table->uuid('class_id')->nullable(); // ← class_id nullable (badge global possible)

            // Numéro unique lisible
            $table->string('badge_number')->unique(); // BADGE-2025-0001

            // QR Code
            $table->string('qr_code_path')->nullable();

            // Statut
            $table->enum('statut', [
                'actif',
                'expire',
                'revoque',
            ])->default('actif');

            // Validité
            $table->date('date_emission');
            $table->date('date_expiration')->nullable();

            // Révocation
            $table->uuid('revoque_par')->nullable();
            $table->timestamp('revoque_le')->nullable();
            $table->text('raison_revocation')->nullable();

            // Données supplémentaires
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')
                  ->references('id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('class_id')
                  ->references('id')->on('classes')
                  ->nullOnDelete();         // ← nullable + nullOnDelete cohérent

            $table->foreign('revoque_par')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->index('student_id');
            $table->index('statut');
            $table->index('date_expiration');
            $table->index(['student_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};