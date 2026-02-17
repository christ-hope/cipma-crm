<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasUuid;

    protected $fillable = [
        'payment_plan_id',
        'numero',
        'montant',
        'montant_paye',
        'date_echeance',
        'date_paiement',
        'paye',
        'rappels_envoyes',
        'dernier_rappel_le',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'date_echeance' => 'date',
        'date_paiement' => 'datetime',
        'paye' => 'boolean',
        'dernier_rappel_le' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function paymentPlan()
    {
        return $this->belongsTo(PaymentPlan::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeUnpaid($query)
    {
        return $query->where('paye', false);
    }
    public function scopeOverdue($query)
    {
        return $query->where('paye', false)->where('date_echeance', '<', now());
    }

    // ─── Methods ─────────────────────────────────────────────────────────────

    public function markAsPaid(): void
    {
        $this->update([
            'paye' => true,
            'date_paiement' => now(),
            'montant_paye' => $this->montant,
        ]);
    }

    public function isOverdue(): bool
    {
        return !$this->paye && $this->date_echeance->isPast();
    }
}