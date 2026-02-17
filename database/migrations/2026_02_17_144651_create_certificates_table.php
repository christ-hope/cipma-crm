<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Numéro de série unique lisible
            $table->string('numero_unique')->unique(); // CERT-2025-0001

            // Relations principales
            $table->uuid('student_id');
            $table->uuid('class_id');       // ← class_id (pas session_id)
            $table->uuid('enrollment_id');

            // Informations du certificat
            $table->string('titre');
            $table->text('description')->nullable();

            // Résultat
            $table->decimal('note_finale', 5, 2);
            $table->string('mention')->nullable(); // Passable, Bien, Très Bien, Excellent

            // Intégrité & vérification
            $table->string('hash', 64)->unique(); // SHA-256
            $table->string('qr_code_path')->nullable();
            $table->string('pdf_path')->nullable();

            // Statut
            $table->enum('statut', ['emis', 'revoque'])->default('emis');

            // Émission
            $table->uuid('emis_par');
            $table->timestamp('emis_le');

            // Révocation (traçable et immuable)
            $table->uuid('revoque_par')->nullable();
            $table->timestamp('revoque_le')->nullable();
            $table->text('raison_revocation')->nullable();

            // Données supplémentaires
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')
                  ->references('id')->on('students')
                  ->onDelete('restrict'); // On ne supprime pas les étudiants qui ont des certificats

            $table->foreign('class_id')
                  ->references('id')->on('classes')
                  ->onDelete('restrict');

            $table->foreign('enrollment_id')
                  ->references('id')->on('enrollments')
                  ->onDelete('restrict');

            $table->foreign('emis_par')
                  ->references('id')->on('users')
                  ->onDelete('restrict');

            $table->foreign('revoque_par')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->index('numero_unique');
            $table->index('hash');
            $table->index('student_id');
            $table->index('statut');
            $table->index('emis_le');
            $table->index(['student_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};