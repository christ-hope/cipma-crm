<?php

namespace App;

enum TransactionMethod: string
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case CREDIT_CARD = 'credit_card';
    case MOBILE_MONEY = 'mobile_money';
    case CHECK = 'check';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Espèces',
            self::BANK_TRANSFER => 'Virement bancaire',
            self::CREDIT_CARD => 'Carte bancaire',
            self::MOBILE_MONEY => 'Mobile Money',
            self::CHECK => 'Chèque',
            self::OTHER => 'Autre',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH => '💵',
            self::BANK_TRANSFER => '🏦',
            self::CREDIT_CARD => '💳',
            self::MOBILE_MONEY => '📱',
            self::CHECK => '📄',
            self::OTHER => '💰',
        };
    }

    /** Une référence externe (numéro de virement, etc.) est attendue */
    public function requiresReference(): bool
    {
        return in_array($this, [
            self::BANK_TRANSFER,
            self::CREDIT_CARD,
            self::MOBILE_MONEY,
            self::CHECK,
        ]);
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => [
                'value' => $c->value,
                'label' => $c->label(),
                'icon' => $c->icon(),
                'requires_reference' => $c->requiresReference(),
            ],
            self::cases()
        );
    }
}