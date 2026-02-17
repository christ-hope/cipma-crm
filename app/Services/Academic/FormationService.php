<?php

namespace App\Services\Academic;

use App\Models\Formation;

class FormationService
{
    public function list(array $filters = [])
    {
        return Formation::with('formationType')
            ->when(isset($filters['type_slug']), fn($q) =>
                $q->whereHas('formationType', fn($q2) => $q2->where('slug', $filters['type_slug'])))
            ->when(isset($filters['active_only']), fn($q) => $q->active())
            ->when(isset($filters['search']), fn($q) =>
                $q->where('name', 'like', "%{$filters['search']}%"))
            ->paginate($filters['per_page'] ?? 20);
    }

    public function find(string $id): Formation
    {
        return Formation::with(['formationType', 'classes', 'validationRule'])->findOrFail($id);
    }

    public function create(array $data): Formation
    {
        return Formation::create($data);
    }

    public function update(string $id, array $data): Formation
    {
        $formation = Formation::findOrFail($id);
        $formation->update($data);
        return $formation->fresh(['formationType']);
    }

    public function delete(string $id): void
    {
        $formation = Formation::findOrFail($id);

        if ($formation->classes()->whereIn('status', ['registration_open', 'in_progress'])->exists()) {
            throw new \Exception('Impossible de supprimer une formation avec des classes actives.');
        }

        $formation->delete();
    }
}
