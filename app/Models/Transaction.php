<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\TransactionMethod;
use App\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'payment_plan_id',
        'installment_id',
        'reference',
        'montant',
        'currency',
        'methode',
        'statut',
        'payment_provider',
        'provider_transaction_id',
        'payment_details',
        'receipt_path',
        'processed_by',
        'processed_at',
        'refunded_by',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'processed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'methode' => TransactionMethod::class,
        'statut' => TransactionStatus::class,  
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function paymentPlan()
    {
        return $this->belongsTo(PaymentPlan::class);
    }
    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    public function refundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->where('statut', TransactionStatus::COMPLETED);
    }

    // ─── Methods ─────────────────────────────────────────────────────────────

    public function isSuccessful(): bool
    {
        return $this->statut->isSuccessful();
    }

    public static function generateReference(): string
    {
        return 'TRX-' . strtoupper(substr(md5(uniqid('', true)), 0, 12));
    }
}