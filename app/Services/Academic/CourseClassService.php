<?php

namespace App\Services\Academic;

use App\Models\CourseClass;

class CourseClassService
{
    public function list(array $filters = [])
    {
        return CourseClass::with(['formation.formationType', 'instructor'])
            ->when(isset($filters['formation_id']), fn($q) => $q->where('formation_id', $filters['formation_id']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['upcoming']), fn($q) => $q->upcoming())
            ->paginate($filters['per_page'] ?? 20);
    }

    public function find(string $id): CourseClass
    {
        return CourseClass::with(['formation.formationType', 'instructor', 'enrollments.student'])->findOrFail($id);
    }

    public function create(array $data): CourseClass
    {
        return CourseClass::create($data);
    }

    public function update(string $id, array $data): CourseClass
    {
        $class = CourseClass::findOrFail($id);
        $class->update($data);
        return $class->fresh();
    }

    public function delete(string $id): void
    {
        $class = CourseClass::findOrFail($id);

        if ($class->enrollments()->where('status', 'active')->exists()) {
            throw new \Exception('Impossible de supprimer une classe avec des inscriptions actives.');
        }

        $class->delete();
    }
}
