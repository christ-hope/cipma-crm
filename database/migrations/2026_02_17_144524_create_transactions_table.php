<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('payment_plan_id');
            $table->uuid('installment_id')->nullable();

            // Référence unique de la transaction
            $table->string('reference')->unique();

            // Montants
            $table->decimal('montant', 12, 2);
            $table->string('currency', 3)->default('XOF');

            // Méthode
            $table->enum('methode', [
                'cash',
                'bank_transfer',
                'credit_card',
                'mobile_money',
                'check',
                'other',
            ]);

            // Statut
            $table->enum('statut', [
                'pending',
                'completed',
                'failed',
                'cancelled',
                'refunded',
            ])->default('pending');

            // Infos fournisseur externe (optionnel)
            $table->string('payment_provider')->nullable();
            $table->string('provider_transaction_id')->nullable();
            $table->text('payment_details')->nullable();

            // Justificatif (upload fichier via Spatie Media)
            $table->string('receipt_path')->nullable();

            // Traçabilité
            $table->uuid('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();

            // Remboursement éventuel
            $table->uuid('refunded_by')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payment_plan_id')
                  ->references('id')->on('payment_plans')
                  ->onDelete('cascade');

            $table->foreign('installment_id')
                  ->references('id')->on('installments')
                  ->nullOnDelete();

            $table->foreign('processed_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->foreign('refunded_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->index('reference');
            $table->index('statut');
            $table->index('processed_at');
            $table->index(['payment_plan_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};