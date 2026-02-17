<?php
namespace App\Services\Academic;

use App\CertificateMention;
use App\CertificateStatus;
use App\Events\CertificateRequested;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class CertificateService
{
    public function __construct(private ValidationEngine $validationEngine) {}

    public function request(string $enrollmentId, string $issuedBy): Certificate
    {
        return DB::transaction(function () use ($enrollmentId, $issuedBy) {
            $enrollment = Enrollment::with(['student', 'class.formation.formationType'])->findOrFail($enrollmentId);

            if (! $enrollment->class->formation->requiresCertification()) {
                throw new \Exception('Ce type de formation ne délivre pas de certificat.');
            }

            $check = $this->validationEngine->check($enrollmentId);
            if (! $check['validated']) {
                throw new \Exception('Les conditions de validation ne sont pas remplies : ' . implode(', ', $check['missing']));
            }

            if (Certificate::where('enrollment_id', $enrollmentId)->where('statut', CertificateStatus::EMIS)->exists()) {
                throw new \Exception('Un certificat a déjà été émis pour cette inscription.');
            }

            $moyenne   = collect($check['reasons']['note']['moyenne']);
            $noteFin   = (float) $check['reasons']['note']['moyenne'];

            $certificate = Certificate::create([
                'numero_unique' => Certificate::generateUniqueNumber(),
                'student_id'    => $enrollment->student_id,
                'class_id'      => $enrollment->class_id,
                'enrollment_id' => $enrollmentId,
                'titre'         => 'Certificat ' . $enrollment->class->formation->name,
                'note_finale'   => $noteFin,
                'mention'       => CertificateMention::fromNote($noteFin),
                'statut'        => CertificateStatus::EMIS,
                'emis_par'      => $issuedBy,
                'emis_le'       => now(),
            ]);

            CertificateRequested::dispatch($certificate);

            return $certificate->fresh(['student', 'class.formation']);
        });
    }

    public function revoke(string $id, string $userId, string $reason): Certificate
    {
        $certificate = Certificate::findOrFail($id);

        if (! $certificate->isValid()) {
            throw new \Exception('Ce certificat est déjà révoqué.');
        }

        $certificate->revoke($userId, $reason);
        return $certificate->fresh();
    }

    public function verify(string $id): array
    {
        $certificate = Certificate::with(['student', 'class.formation'])->findOrFail($id);

        return [
            'valid'       => $certificate->isValid(),
            'numero'      => $certificate->numero_unique,
            'student'     => ['name' => $certificate->student->full_name, 'number' => $certificate->student->student_number],
            'formation'   => $certificate->class->formation->name,
            'note_finale' => $certificate->note_finale,
            'mention'     => $certificate->mention->label(),
            'emis_le'     => $certificate->emis_le->format('d/m/Y'),
            'statut'      => $certificate->statut->label(),
            'revoque_le'  => $certificate->revoque_le?->format('d/m/Y'),
            'raison'      => $certificate->raison_revocation,
        ];
    }

    public function getForStudent(string $studentId)
    {
        return Certificate::with(['class.formation'])
            ->where('student_id', $studentId)
            ->latest('emis_le')
            ->get();
    }
}