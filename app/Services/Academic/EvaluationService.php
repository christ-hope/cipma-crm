<?php

namespace App\Services\Academic;

use App\Models\StudentEvaluation;
use App\ValidationStatus;

class EvaluationService
{
    public function manualEntry(array $data, string $userId): StudentEvaluation
    {
        $existing = StudentEvaluation::where('student_id', $data['student_id'])
            ->where('class_id', $data['class_id'])
            ->when(isset($data['evaluation_id']), fn($q) => $q->where('evaluation_id', $data['evaluation_id']))
            ->first();

        if ($existing) {
            $existing->update([...$data, 'saisi_par' => $userId, 'saisi_le' => now()]);
            return $existing->fresh();
        }

        return StudentEvaluation::create([
            ...$data,
            'statut_validation' => ValidationStatus::EN_COURS,
            'saisi_par' => $userId,
            'saisi_le' => now(),
        ]);
    }

    public function validate(string $id, string $userId): StudentEvaluation
    {
        $eval = StudentEvaluation::findOrFail($id);
        $eval->validate($userId);
        return $eval->fresh();
    }

    public function invalidate(string $id, string $userId): StudentEvaluation
    {
        $eval = StudentEvaluation::findOrFail($id);
        $eval->invalidate($userId);
        return $eval->fresh();
    }

    public function getForClass(string $classId)
    {
        return StudentEvaluation::with(['student', 'evaluation', 'enteredBy'])
            ->where('class_id', $classId)
            ->get()
            ->groupBy('student_id');
    }

    public function getForStudent(string $studentId)
    {
        return StudentEvaluation::with(['class.formation', 'evaluation'])
            ->where('student_id', $studentId)
            ->get();
    }
}
