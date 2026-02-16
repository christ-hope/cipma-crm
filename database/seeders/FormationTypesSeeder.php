<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormationType;
use Illuminate\Support\Str;

class FormationTypesSeeder extends Seeder
{
    public function run(): void
    {
        FormationType::create([
            'id' => (string) Str::uuid(),
            'name' => 'Formation CIPMA',
            'slug' => 'internal',
            'description' => 'Formations gérées entièrement dans le CRM CIPMA avec évaluations détaillées',
            'requires_certification' => true,
            'evaluation_mode' => 'crm',
            'is_active' => true,
        ]);

        FormationType::create([
            'id' => (string) Str::uuid(),
            'name' => 'Formation Moodle',
            'slug' => 'moodle',
            'description' => 'Formations dispensées sur Moodle, import note finale uniquement',
            'requires_certification' => true,
            'evaluation_mode' => 'external',
            'is_active' => true,
        ]);
    }
}