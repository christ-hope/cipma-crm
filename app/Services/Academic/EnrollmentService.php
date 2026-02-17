<?php

namespace App\Services\Academic;

use App\EnrollmentStatus;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function enroll(string $studentId, string $classId): Enrollment
    {
        return DB::transaction(function () use ($studentId, $classId) {
            $student = Student::findOrFail($studentId);
            $class = CourseClass::findOrFail($classId);

            if (!$class->status->acceptsEnrollments()) {
                throw new \Exception('Les inscriptions sont fermées pour cette classe.');
            }

            if (!$class->hasAvailableSeats()) {
                throw new \Exception('Cette classe est complète.');
            }

            if (Enrollment::where('student_id', $studentId)->where('class_id', $classId)->exists()) {
                throw new \Exception('L\'étudiant est déjà inscrit à cette classe.');
            }

            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'class_id' => $classId,
                'status' => EnrollmentStatus::PENDING,
                'enrolled_at' => now(),
            ]);

            $class->incrementEnrollment();

            // Créer le plan de paiement automatiquement
            $this->paymentService->createPlan($enrollment, $class->formation);

            return $enrollment->fresh(['student', 'class.formation']);
        });
    }

    public function updateAttendance(string $id, int $attended, int $total): Enrollment
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->sessions_attended = $attended;
        $enrollment->sessions_total = $total;
        $enrollment->save();
        $enrollment->recalculateAttendance();
        return $enrollment->fresh();
    }

    public function withdraw(string $id): Enrollment
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->withdraw();
        $enrollment->class->decrementEnrollment();
        return $enrollment->fresh();
    }

    public function list(array $filters = [])
    {
        return Enrollment::with(['student', 'class.formation'])
            ->when(isset($filters['class_id']), fn($q) => $q->where('class_id', $filters['class_id']))
            ->when(isset($filters['student_id']), fn($q) => $q->where('student_id', $filters['student_id']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->paginate($filters['per_page'] ?? 20);
    }
}
