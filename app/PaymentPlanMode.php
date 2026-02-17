<?php

namespace App;

enum PaymentPlanMode: string
{
    case TOTAL = 'total';
    case ECHELONNE = 'echelonne';

    public function label(): string
    {
        return match ($this) {
            self::TOTAL => 'Paiement total',
            self::ECHELONNE => 'Paiement échelonné',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TOTAL => 'Règlement en une seule fois',
            self::ECHELONNE => 'Règlement en plusieurs versements',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => [
                'value' => $c->value,
                'label' => $c->label(),
                'description' => $c->description(),
            ],
            self::cases()
        );
    }
}