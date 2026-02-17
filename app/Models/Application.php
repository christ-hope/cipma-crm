<?php

namespace App\Models;

use App\ApplicationStatus;
use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'birth_place',
        'nationality',
        'address',
        'city',
        'postal_code',
        'country',
        'last_diploma',
        'institution',
        'graduation_year',
        'academic_background',
        'requested_formations',
        'status',
        'legal_declaration',
        'declaration_date',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'rejection_reason',
        'student_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'requested_formations' => 'array',
        'legal_declaration' => 'boolean',
        'declaration_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'status' => ApplicationStatus::class,
    ];

    // Relations
    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', ApplicationStatus::SUBMITTED);
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', ApplicationStatus::UNDER_REVIEW);
    }

    // Methods
    public function approve(string $userId): bool
    {
        $this->update([
            'status' => ApplicationStatus::APPROVED,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);

        return true;
    }

    public function reject(string $userId, string $reason): bool
    {
        $this->update([
            'status' => ApplicationStatus::REJECTED,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }
}
