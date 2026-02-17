<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\EvaluationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'class_id',
        'name',
        'description',
        'type',
        'weight',
        'max_score',
        'passing_score',
        'evaluation_date',
        'is_mandatory',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'max_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'evaluation_date' => 'date',
        'is_mandatory' => 'boolean',
        'type' => EvaluationType::class,
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function class()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }
    public function studentEvaluations()
    {
        return $this->hasMany(StudentEvaluation::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }
}