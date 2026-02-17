<?php

namespace App\Models;

use App\BadgeStatus;
use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Badge extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'badge_number',
        'qr_code_path',
        'statut',
        'date_emission',
        'date_expiration',
        'revoque_par',
        'revoque_le',
        'raison_revocation',
        'metadata',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
        'revoque_le' => 'datetime',
        'metadata' => 'array',
        'statut' => BadgeStatus::class,   // ← ENUM CAST
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function class()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }
    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoque_par');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('statut', BadgeStatus::ACTIF);
    }
    public function scopeExpired($query)
    {
        return $query->where('statut', BadgeStatus::EXPIRE);
    }

    // ─── Methods ─────────────────────────────────────────────────────────────

    public function isValid(): bool
    {
        return $this->statut->isValid();
    }

    public function checkExpiration(): void
    {
        if (
            $this->statut === BadgeStatus::ACTIF
            && $this->date_expiration
            && $this->date_expiration->isPast()
        ) {
            $this->update(['statut' => BadgeStatus::EXPIRE]);
        }
    }

    public static function generateBadgeNumber(): string
    {
        $year = date('Y');
        $last = static::whereYear('date_emission', $year)->orderByDesc('date_emission')->first();
        $seq = $last ? ((int) substr($last->badge_number, -4)) + 1 : 1;
        return sprintf('BADGE-%s-%04d', $year, $seq);
    }

    public function revoke(string $userId, string $reason): void
    {
        $this->update([
            'statut' => BadgeStatus::REVOQUE,
            'revoque_par' => $userId,
            'revoque_le' => now(),
            'raison_revocation' => $reason,
        ]);
    }
}

