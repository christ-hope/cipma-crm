<?php

namespace App;

enum PaymentPlanStatus: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case COMPLETED = 'completed';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::PARTIAL => 'Partiellement payé',
            self::COMPLETED => 'Payé intégralement',
            self::OVERDUE => 'En retard',
            self::CANCELLED => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PARTIAL => 'blue',
            self::COMPLETED => 'green',
            self::OVERDUE => 'red',
            self::CANCELLED => 'gray',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::COMPLETED;
    }

    /** Peut encore recevoir un paiement */
    public function canReceivePayment(): bool
    {
        return in_array($this, [self::PENDING, self::PARTIAL, self::OVERDUE]);
    }

    /** Nécessite une action de la comptabilité */
    public function requiresAction(): bool
    {
        return $this === self::OVERDUE;
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases()
        );
    }
}