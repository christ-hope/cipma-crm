<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\EvaluationSource;
use App\ValidationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEvaluation extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'evaluation_id',
        'source',
        'score',
        'note_finale',
        'statut_validation',
        'presence',
        'commentaire',
        'saisi_par',
        'saisi_le',
        'valide_par',
        'valide_le',
        'metadata',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'note_finale' => 'decimal:2',
        'presence' => 'boolean',
        'metadata' => 'array',
        'saisi_le' => 'datetime',
        'valide_le' => 'datetime',
        'source' => EvaluationSource::class,
        'statut_validation' => ValidationStatus::class,
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'saisi_par');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // Methods
    public function validate(string $userId): void
    {
        $this->update([
            'statut_validation' => ValidationStatus::VALIDE,
            'valide_par' => $userId,
            'valide_le' => now(),
        ]);
    }
}
