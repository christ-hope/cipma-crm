<?php

namespace App\Services\Academic;

use App\Models\FormationType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FormationTypeService
{
    /**
     * Créer un nouveau type de formation
     */
    public function create(array $data, string $userId): FormationType
    {
        return DB::transaction(function () use ($data, $userId) {
            $slug = Str::slug($data['name']);

            // Vérifier unicité du slug
            $existingSlug = FormationType::where('slug', $slug)->exists();
            if ($existingSlug) {
                $slug = $slug . '-' . Str::random(4);
            }

            return FormationType::create([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'requires_certification' => $data['requires_certification'] ?? true,
                'evaluation_mode' => $data['evaluation_mode'] ?? 'crm',
                'is_active' => true,
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Mettre à jour un type de formation
     */
    public function update(string $typeId, array $data): FormationType
    {
        $type = FormationType::findOrFail($typeId);

        $updateData = [
            'name' => $data['name'] ?? $type->name,
            'description' => $data['description'] ?? $type->description,
            'requires_certification' => $data['requires_certification'] ?? $type->requires_certification,
            'evaluation_mode' => $data['evaluation_mode'] ?? $type->evaluation_mode,
            'is_active' => $data['is_active'] ?? $type->is_active,
        ];

        // Mettre à jour le slug si le nom change
        if (isset($data['name']) && $data['name'] !== $type->name) {
            $updateData['slug'] = Str::slug($data['name']);
        }

        $type->update($updateData);

        return $type->fresh();
    }

    /**
     * Désactiver un type de formation
     */
    public function deactivate(string $typeId): FormationType
    {
        $type = FormationType::findOrFail($typeId);

        // Vérifier si des formations actives utilisent ce type
        $activeFormations = $type->formations()->where('is_active', true)->count();

        if ($activeFormations > 0) {
            throw new \Exception(
                "Impossible de désactiver ce type car {$activeFormations} formation(s) active(s) l'utilise(nt)"
            );
        }

        $type->update(['is_active' => false]);

        return $type;
    }

    /**
     * Activer un type de formation
     */
    public function activate(string $typeId): FormationType
    {
        $type = FormationType::findOrFail($typeId);
        $type->update(['is_active' => true]);

        return $type;
    }

    /**
     * Supprimer un type de formation
     */
    public function delete(string $typeId): bool
    {
        $type = FormationType::findOrFail($typeId);

        // Ne pas permettre suppression des types système
        $systemTypes = ['internal', 'moodle'];
        if (in_array($type->slug, $systemTypes)) {
            throw new \Exception('Impossible de supprimer un type système');
        }

        // Vérifier si des formations utilisent ce type
        if ($type->formations()->count() > 0) {
            throw new \Exception('Impossible de supprimer un type utilisé par des formations');
        }

        return $type->delete();
    }

    /**
     * Lister les types de formations
     */
    public function list(bool $activeOnly = false)
    {
        $query = FormationType::with('creator');

        if ($activeOnly) {
            $query->active();
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Obtenir un type par slug
     */
    public function getBySlug(string $slug): ?FormationType
    {
        return FormationType::where('slug', $slug)->first();
    }

    /**
     * Statistiques d'utilisation d'un type
     */
    public function getTypeStats(string $typeId): array
    {
        $type = FormationType::with('formations')->findOrFail($typeId);

        return [
            'type' => $type,
            'total_formations' => $type->formations()->count(),
            'active_formations' => $type->formations()->where('is_active', true)->count(),
            'total_classes' => $type->formations()
                ->withCount('classes')
                ->get()
                ->sum('classes_count'),
        ];
    }
}