<?php 
namespace App\Services\Academic;

use App\Events\PaymentReceived;
use App\Models\Enrollment;
use App\Models\Formation;
use App\Models\Installment;
use App\Models\PaymentPlan;
use App\Models\Transaction;
use App\PaymentPlanMode;
use App\PaymentPlanStatus;
use App\TransactionStatus;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createPlan(Enrollment $enrollment, Formation $formation, int $installments = 1): PaymentPlan
    {
        $mode = $installments > 1 ? PaymentPlanMode::ECHELONNE : PaymentPlanMode::TOTAL;
        $minimumActivation = $installments > 1
            ? round($formation->price * 0.30, 2)
            : $formation->price;

        $plan = PaymentPlan::create([
            'student_id'                 => $enrollment->student_id,
            'enrollment_id'              => $enrollment->id,
            'montant_total'              => $formation->price,
            'montant_paye'               => 0,
            'montant_restant'            => $formation->price,
            'currency'                   => $formation->currency,
            'mode'                       => $mode,
            'montant_minimum_activation' => $minimumActivation,
            'statut'                     => PaymentPlanStatus::PENDING,
        ]);

        if ($installments > 1) {
            $this->createInstallments($plan, $installments);
        } else {
            Installment::create([
                'payment_plan_id' => $plan->id,
                'numero'          => 1,
                'montant'         => $formation->price,
                'date_echeance'   => now()->addDays(7),
            ]);
        }

        return $plan->fresh(['installments']);
    }

    private function createInstallments(PaymentPlan $plan, int $count): void
    {
        $perInstallment = round($plan->montant_total / $count, 2);

        for ($i = 1; $i <= $count; $i++) {
            $amount = ($i === $count)
                ? $plan->montant_total - ($perInstallment * ($count - 1))
                : $perInstallment;

            Installment::create([
                'payment_plan_id' => $plan->id,
                'numero'          => $i,
                'montant'         => $amount,
                'date_echeance'   => now()->addMonths($i),
            ]);
        }
    }

    public function pay(string $planId, float $amount, string $method, string $processedBy, ?string $installmentId = null): Transaction
    {
        return DB::transaction(function () use ($planId, $amount, $method, $processedBy, $installmentId) {
            $plan = PaymentPlan::lockForUpdate()->findOrFail($planId);

            if (! $plan->statut->canReceivePayment()) {
                throw new \Exception('Ce plan de paiement ne peut plus recevoir de paiement.');
            }
            if ($amount > $plan->montant_restant) {
                throw new \Exception("Le montant dépasse le reste dû ({$plan->montant_restant} {$plan->currency}).");
            }

            $transaction = Transaction::create([
                'payment_plan_id' => $planId,
                'installment_id'  => $installmentId,
                'reference'       => Transaction::generateReference(),
                'montant'         => $amount,
                'currency'        => $plan->currency,
                'methode'         => $method,
                'statut'          => TransactionStatus::COMPLETED,
                'processed_by'    => $processedBy,
                'processed_at'    => now(),
            ]);

            if ($installmentId) {
                Installment::findOrFail($installmentId)->markAsPaid();
            }

            $activated = $plan->addPayment($amount);

            if ($activated) {
                $plan->enrollment->activate();
            }

            PaymentReceived::dispatch($transaction);

            return $transaction->fresh(['paymentPlan.enrollment']);
        });
    }

    public function getPlanDetails(string $planId): array
    {
        $plan = PaymentPlan::with(['installments', 'transactions', 'enrollment.class.formation'])->findOrFail($planId);

        return [
            'plan'         => $plan,
            'progress'     => $plan->progress_percentage,
            'next_due'     => $plan->next_due_installment,
            'overdue'      => $plan->installments->filter(fn($i) => $i->isOverdue())->count(),
        ];
    }
}