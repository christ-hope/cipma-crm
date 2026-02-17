<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('payment_plan_id');

            // Numéro d'ordre de l'échéance
            $table->unsignedSmallInteger('numero')->default(1);

            // Montants
            $table->decimal('montant', 12, 2);
            $table->decimal('montant_paye', 12, 2)->default(0);

            // Dates
            $table->date('date_echeance');
            $table->timestamp('date_paiement')->nullable();

            // Statut
            $table->boolean('paye')->default(false);

            // Rappels envoyés
            $table->unsignedTinyInteger('rappels_envoyes')->default(0);
            $table->timestamp('dernier_rappel_le')->nullable();

            $table->timestamps();

            $table->foreign('payment_plan_id')
                ->references('id')->on('payment_plans')
                ->onDelete('cascade');

            $table->index('payment_plan_id');
            $table->index('date_echeance');
            $table->index('paye');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};