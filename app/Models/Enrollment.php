<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\EnrollmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'status',
        'enrolled_at',
        'activated_at',
        'completed_at',
        'attendance_rate',
        'sessions_attended',
        'sessions_total',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
        'attendance_rate' => 'decimal:2',
        'status' => EnrollmentStatus::class,
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

    // Methods
    public function activate(): void
    {
        $this->update([
            'status' => EnrollmentStatus::ACTIVE,
            'activated_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => EnrollmentStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function calculateAttendanceRate(): void
    {
        if ($this->sessions_total > 0) {
            $rate = ($this->sessions_attended / $this->sessions_total) * 100;
            $this->update(['attendance_rate' => round($rate, 2)]);
        }
    }
}
