<?php

namespace App\Services\Academic;

use App\Models\Application;
use App\Models\Student;
use App\Models\User;

class StudentService
{
    public function createFromApplication(Application $application, User $user): Student
    {
        return Student::create([
            'user_id' => $user->id,
            'student_number' => Student::generateStudentNumber(),
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'email' => $application->email,
            'phone' => $application->phone,
            'birth_date' => $application->birth_date,
            'birth_place' => $application->birth_place,
            'nationality' => $application->nationality,
            'address' => $application->address,
            'city' => $application->city,
            'postal_code' => $application->postal_code,
            'country' => $application->country,
            'last_diploma' => $application->last_diploma,
            'institution' => $application->institution,
            'graduation_year' => $application->graduation_year,
        ]);
    }

    public function update(string $id, array $data): Student
    {
        $student = Student::findOrFail($id);
        $student->update($data);
        return $student->fresh();
    }

    public function getDashboard(Student $student): array
    {
        $student->load([
            'enrollments.class.formation.formationType',
            'paymentPlans.installments',
            'certificates.class.formation',
            'badges',
        ]);

        return [
            'student' => $student,
            'active_enrollments' => $student->enrollments->whereIn('status', ['active', 'pending'])->values(),
            'completed_formations' => $student->enrollments->where('status', 'completed')->values(),
            'pending_payments' => $student->paymentPlans->whereIn('statut', ['pending', 'partial', 'overdue'])->values(),
            'certificates' => $student->certificates->where('statut', 'emis')->values(),
            'badges' => $student->badges->where('statut', 'actif')->values(),
        ];
    }

    public function find(string $id): Student
    {
        return Student::with(['user', 'enrollments.class.formation'])->findOrFail($id);
    }

    public function list(array $filters = [])
    {
        return Student::with('user')
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where(fn($q2) => $q2
                    ->where('first_name', 'like', "%{$filters['search']}%")
                    ->orWhere('last_name', 'like', "%{$filters['search']}%")
                    ->orWhere('student_number', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%"));
            })
            ->paginate($filters['per_page'] ?? 20);
    }
}
