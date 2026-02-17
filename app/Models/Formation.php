<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\FormationMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formation extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'formation_type_id',
        'code',
        'name',
        'description',
        'mode',
        'duration_hours',
        'duration_days',
        'price',
        'currency',
        'max_students',
        'prerequisites',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'prerequisites' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'mode' => FormationMode::class,
    ];

    // Relations
    public function formationType()
    {
        return $this->belongsTo(FormationType::class);
    }

    public function classes()
    {
        return $this->hasMany(CourseClass::class);
    }

    public function validationRule()
    {
        return $this->hasOne(ValidationRule::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function requiresCertification(): bool
    {
        return $this->formationType->requires_certification;
    }

    public function getEvaluationMode(): string
    {
        return $this->formationType->evaluation_mode;
    }
}
