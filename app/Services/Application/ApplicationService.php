<?php

namespace App\Services\Application;

use App\Models\Application;
use App\ApplicationStatus;
use App\Events\ApplicationApproved;
use App\Events\ApplicationSubmitted;
use App\Models\User;
use App\Services\Academic\StudentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApplicationService
{
    public function __construct(private StudentService $studentService)
    {
    }

    public function create(array $data): Application
    {
        return DB::transaction(function () use ($data) {
            $application = Application::create([
                ...$data,
                'status' => ApplicationStatus::SUBMITTED,
                'declaration_date' => now(),
            ]);

            ApplicationSubmitted::dispatch($application);

            return $application;
        });
    }

    public function approve(string $id, string $reviewerId): Application
    {
        return DB::transaction(function () use ($id, $reviewerId) {
            $application = Application::findOrFail($id);

            if (!$application->status->isReviewable()) {
                throw new \Exception('Cette candidature ne peut plus être traitée.');
            }

            $tempPassword = Str::random(12);

            $user = User::create([
                'email' => $application->email,
                'password' => Hash::make($tempPassword),
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'phone' => $application->phone,
                'is_active' => true,
                'must_change_password' => true,
            ]);
            $user->assignRole('student');

            $student = $this->studentService->createFromApplication($application, $user);

            $application->update([
                'status' => ApplicationStatus::APPROVED,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
                'student_id' => $student->id,
            ]);

            ApplicationApproved::dispatch($application->fresh(['student', 'reviewer']), $tempPassword);
            
            return $application->fresh(['student', 'reviewer']);
        });
    }

    public function reject(string $id, string $reviewerId, string $reason): Application
    {
        $application = Application::findOrFail($id);

        if (!$application->status->isReviewable()) {
            throw new \Exception('Cette candidature ne peut plus être traitée.');
        }

        $application->update([
            'status' => ApplicationStatus::REJECTED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $application->fresh();
    }

    public function requestInfo(string $id, string $reviewerId, string $notes): Application
    {
        $application = Application::findOrFail($id);
        $application->requestInfo($reviewerId, $notes);
        return $application->fresh();
    }

    public function uploadDocuments(string $id, array $files): Application
    {
        $application = Application::findOrFail($id);

        foreach ($files as $type => $file) {
            /** @var UploadedFile $file */
            $path = $file->store("applications/{$id}/documents", 'private');

            $application->documents()->create([
                'type' => $type,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return $application->fresh(['documents']);
    }

    public function list(array $filters = [])
    {
        return Application::with(['reviewer', 'student'])
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where(fn($q2) => $q2
                    ->where('first_name', 'like', "%{$filters['search']}%")
                    ->orWhere('last_name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%"));
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 20);
    }
}
