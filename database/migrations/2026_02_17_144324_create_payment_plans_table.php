<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('student_id');
            $table->uuid('enrollment_id');

            // Montants
            $table->decimal('montant_total', 12, 2);
            $table->decimal('montant_paye', 12, 2)->default(0);
            $table->decimal('montant_restant', 12, 2);
            $table->string('currency', 3)->default('XOF');

            // Mode paiement
            $table->enum('mode', ['total', 'echelonne'])->default('total');

            // Seuil d'activation inscription
            $table->decimal('montant_minimum_activation', 12, 2)->nullable();
            $table->boolean('inscription_activee')->default(false);

            // Statut
            $table->enum('statut', [
                'pending',
                'partial',
                'completed',
                'overdue',
                'cancelled',
            ])->default('pending');

            // Dates
            $table->timestamp('completed_at')->nullable();

            // Traçabilité
            $table->uuid('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')
                ->references('id')->on('students')
                ->onDelete('cascade');

            $table->foreign('enrollment_id')
                ->references('id')->on('enrollments')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->index('student_id');
            $table->index('enrollment_id');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};