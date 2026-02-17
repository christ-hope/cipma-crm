<?php

namespace App\Models;

use App\ClassStatus;
use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseClass extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'formation_id',
        'code',
        'name',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'location_details',
        'instructor_id',
        'max_students',
        'enrolled_count',
        'status',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'enrolled_count' => 'integer',
        'metadata' => 'array',
        'status' => ClassStatus::class,   // ← ENUM CAST
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'class_id');
    }
    public function studentEvaluations()
    {
        return $this->hasMany(StudentEvaluation::class, 'class_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }
    public function scopeActive($query)
    {
        return $query->whereIn('status', ClassStatus::studentVisible());
    }
    public function scopeInProgress($query)
    {
        return $query->where('status', ClassStatus::IN_PROGRESS);
    }
    public function scopeOpen($query)
    {
        return $query->where('status', ClassStatus::REGISTRATION_OPEN);
    }

    // ─── Methods ─────────────────────────────────────────────────────────────

    public function hasAvailableSeats(): bool
    {
        return !$this->max_students || $this->enrolled_count < $this->max_students;
    }

    public function incrementEnrollment(): void
    {
        $this->increment('enrolled_count');
    }
    public function decrementEnrollment(): void
    {
        $this->decrement('enrolled_count');
    }
}
