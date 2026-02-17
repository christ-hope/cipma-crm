<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\PaymentPlanMode;
use App\PaymentPlanStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentPlan extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'student_id',
        'enrollment_id',
        'montant_total',
        'montant_paye',
        'montant_restant',
        'currency',
        'mode',
        'montant_minimum_activation',
        'inscription_activee',
        'statut',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_restant' => 'decimal:2',
        'montant_minimum_activation' => 'decimal:2',
        'inscription_activee' => 'boolean',
        'completed_at' => 'datetime',
        'mode' => PaymentPlanMode::class,
        'statut' => PaymentPlanStatus::class,
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeOverdue($query)
    {
        return $query->where('statut', PaymentPlanStatus::OVERDUE);
    }
    public function scopePending($query)
    {
        return $query->where('statut', PaymentPlanStatus::PENDING);
    }
    public function scopeCompleted($query)
    {
        return $query->where('statut', PaymentPlanStatus::COMPLETED);
    }

    // ─── Methods ─────────────────────────────────────────────────────────────

    /**
     * Enregistre un paiement et déclenche l'activation de l'inscription si le
     * minimum est atteint. Retourne true si l'inscription vient d'être activée.
     */
    public function addPayment(float $amount): bool
    {
        $amount = number_format($amount, 2, '.', '');
        
        $this->montant_paye = $amount;
        $this->montant_restant -= $amount;

        // Statut du plan
        if ($this->montant_restant <= 0) {
            $this->statut = PaymentPlanStatus::COMPLETED;
            $this->completed_at = Carbon::now();
        } else {
            $this->statut = PaymentPlanStatus::PARTIAL;
        }

        $this->save();

        // Activation inscription si minimum atteint et pas encore activée
        if (!$this->inscription_activee && $this->canActivateEnrollment()) {
            $this->update(['inscription_activee' => true]);
            return true; 
        }

        return false;
    }

    public function canActivateEnrollment(): bool
    {
        if (!$this->montant_minimum_activation) {
            return $this->montant_paye > 0;
        }
        return $this->montant_paye >= $this->montant_minimum_activation;
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->montant_total <= 0)
            return 0;
        return round(($this->montant_paye / $this->montant_total) * 100, 1);
    }

    public function getNextDueInstallmentAttribute(): ?Installment
    {
        return $this->installments()
            ->where('paye', false)
            ->orderBy('date_echeance')
            ->first();
    }

}
