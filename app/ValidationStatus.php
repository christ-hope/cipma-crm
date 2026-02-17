<?php

namespace App;

enum ValidationStatus: string
{
    case EN_COURS   = 'en_cours';
    case VALIDE     = 'valide';
    case NON_VALIDE = 'non_valide';
    case EN_ATTENTE = 'en_attente';

    public function label(): string
    {
        return match($this) {
            self::EN_COURS   => 'En cours',
            self::VALIDE     => 'Validé',
            self::NON_VALIDE => 'Non validé',
            self::EN_ATTENTE => 'En attente de validation manuelle',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::EN_COURS   => 'blue',
            self::VALIDE     => 'green',
            self::NON_VALIDE => 'red',
            self::EN_ATTENTE => 'yellow',
        };
    }

    /** Plus de changement automatique possible */
    public function isTerminal(): bool
    {
        return in_array($this, [self::VALIDE, self::NON_VALIDE]);
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases()
        );
    }
}