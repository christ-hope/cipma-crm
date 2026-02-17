<?php

namespace App\Models;

use App\CertificateMention;
use App\CertificateStatus;
use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'numero_unique',
        'student_id',
        'class_id',
        'enrollment_id',
        'titre',
        'description',
        'note_finale',
        'mention',
        'hash',
        'qr_code_path',
        'pdf_path',
        'statut',
        'emis_par',
        'emis_le',
        'revoque_par',
        'revoque_le',
        'raison_revocation',
        'metadata',
    ];

    protected $casts = [
        'note_finale' => 'decimal:2',
        'metadata' => 'array',
        'emis_le' => 'datetime',
        'revoque_le' => 'datetime',
        'statut' => CertificateStatus::class,
        'mention' => CertificateMention::class,

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

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'emis_par');
    }

    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoque_par');
    }

    // Methods
    public static function generateUniqueNumber(): string
    {
        $year = date('Y');
        $lastCert = static::whereYear('emis_le', $year)
            ->orderBy('emis_le', 'desc')
            ->first();

        $sequence = $lastCert
            ? ((int) substr($lastCert->numero_unique, -4)) + 1
            : 1;

        return sprintf('CERT-%s-%04d', $year, $sequence);
    }

    public function generateHash(): void
    {
        $data = implode('|', [
            $this->id,
            $this->student_id,
            $this->class_id,
            $this->emis_le->timestamp,
        ]);

        $this->hash = hash('sha256', $data);
        $this->save();
    }

    public function revoke(string $userId, string $reason): void
    {
        $this->update([
            'statut' => CertificateStatus::REVOQUE,
            'revoque_par' => $userId,
            'revoque_le' => now(),
            'raison_revocation' => $reason,
        ]);
    }

    public function isValid(): bool
    {
        return $this->statut === CertificateStatus::EMIS;
    }
}
