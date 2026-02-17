<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\StudentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;

class Student extends Model
{
    use HasUuid, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'student_number',
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
        'photo_path',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'graduation_year' => 'integer',
        'status' => StudentStatus::class,

    ];

    // Media Library
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('documents')
            ->useDisk('private');
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function paymentPlans()
    {
        return $this->hasMany(PaymentPlan::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function badges()
    {
        return $this->hasMany(Badge::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Methods
    public static function generateStudentNumber(): string
    {
        $year = date('Y');
        $lastStudent = static::whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->first();

        $sequence = $lastStudent
            ? ((int) substr($lastStudent->student_number, -4)) + 1
            : 1;

        return sprintf('STU-%s-%04d', $year, $sequence);
    }
}
