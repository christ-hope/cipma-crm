<?php

namespace App\Services\Academic;

use App\Models\Enrollment;
use App\PaymentPlanStatus;

class ValidationEngine
{
    public function check(string $enrollmentId): array
    {
        $enrollment = Enrollment::with([
            'student',
            'class.formation.validationRule',
            'class.studentEvaluations',
        ])->findOrFail($enrollmentId);

        $rule = $enrollment->class->formation->validationRule;
        $result = ['validated' => true, 'reasons' => [], 'missing' => []];

        $this->checkNote($enrollment, $rule, $result);
        $this->checkPresence($enrollment, $rule, $result);
        $this->checkPayment($enrollment, $rule, $result);
        $this->checkExams($enrollment, $rule, $result);

        if ($result['validated'] && $rule && $rule->validation_manuelle_responsable) {
            // Attente validation manuelle — ne pas changer le statut
        }

        return $result;
    }

    private function checkNote(Enrollment $enrollment, $rule, array &$result): void
    {
        $min = $rule?->note_minimale ?? 10;
        $evals = $enrollment->class->studentEvaluations
            ->where('student_id', $enrollment->student_id)
            ->where('statut_validation', 'valide');

        $moyenne = $evals->count() > 0 ? $evals->avg('note_finale') : null;

        $valid = $moyenne !== null && $moyenne >= $min;
        $result['reasons']['note'] = [
            'valid' => $valid,
            'moyenne' => $moyenne,
            'minimum' => $min,
            'reason' => $valid ? "Moyenne {$moyenne}/20 ≥ {$min}/20" : "Moyenne insuffisante ({$moyenne}/20 < {$min}/20)",
        ];
        if (!$valid) {
            $result['validated'] = false;
            $result['missing'][] = 'Note minimale non atteinte';
        }
    }

    private function checkPresence(Enrollment $enrollment, $rule, array &$result): void
    {
        $min = $rule?->presence_minimale ?? 80;
        $rate = $enrollment->attendance_rate ?? 0;
        $valid = $rate >= $min;

        $result['reasons']['presence'] = [
            'valid' => $valid,
            'taux' => $rate,
            'minimum' => $min,
            'reason' => $valid ? "Présence {$rate}% ≥ {$min}%" : "Présence insuffisante ({$rate}% < {$min}%)",
        ];
        if (!$valid) {
            $result['validated'] = false;
            $result['missing'][] = 'Taux de présence insuffisant';
        }
    }

    private function checkPayment(Enrollment $enrollment, $rule, array &$result): void
    {
        $plan = $enrollment->student->paymentPlans()->where('enrollment_id', $enrollment->id)->first();
        $valid = $plan && $plan->statut === PaymentPlanStatus::COMPLETED;

        $result['reasons']['paiement'] = [
            'valid' => $valid,
            'montant_paye' => $plan?->montant_paye ?? 0,
            'montant_total' => $plan?->montant_total ?? 0,
            'reason' => $valid ? 'Paiement complet' : 'Paiement incomplet',
        ];
        if (!$valid && ($rule?->paiement_complet_requis ?? true)) {
            $result['validated'] = false;
            $result['missing'][] = 'Paiement non complété';
        }
    }

    private function checkExams(Enrollment $enrollment, $rule, array &$result): void
    {
        if (!($rule?->examens_obligatoires ?? true)) {
            return;
        }

        $mandatory = $enrollment->class->evaluations()->mandatory()->pluck('id');
        $done = $enrollment->class->studentEvaluations
            ->where('student_id', $enrollment->student_id)
            ->where('statut_validation', 'valide')
            ->pluck('evaluation_id');

        $missing = $mandatory->diff($done);
        $valid = $missing->isEmpty();

        $result['reasons']['examens'] = [
            'valid' => $valid,
            'reason' => $valid ? 'Tous les examens obligatoires validés' : "{$missing->count()} examen(s) manquant(s)",
        ];
        if (!$valid) {
            $result['validated'] = false;
            $result['missing'][] = 'Examens obligatoires manquants';
        }
    }

    public function manualValidate(string $enrollmentId, string $userId): Enrollment
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $enrollment->complete();
        return $enrollment->fresh();
    }
}