<?php

namespace App;

enum ClassStatus: string
{
    case PLANNED             = 'planned';
    case REGISTRATION_OPEN   = 'registration_open';
    case REGISTRATION_CLOSED = 'registration_closed';
    case IN_PROGRESS         = 'in_progress';
    case COMPLETED           = 'completed';
    case CANCELLED           = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PLANNED             => 'Planifiée',
            self::REGISTRATION_OPEN   => 'Inscriptions ouvertes',
            self::REGISTRATION_CLOSED => 'Inscriptions fermées',
            self::IN_PROGRESS         => 'En cours',
            self::COMPLETED           => 'Terminée',
            self::CANCELLED           => 'Annulée',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PLANNED             => 'gray',
            self::REGISTRATION_OPEN   => 'green',
            self::REGISTRATION_CLOSED => 'yellow',
            self::IN_PROGRESS         => 'blue',
            self::COMPLETED           => 'purple',
            self::CANCELLED           => 'red',
        };
    }

    /** Accepte de nouvelles inscriptions */
    public function acceptsEnrollments(): bool
    {
        return $this === self::REGISTRATION_OPEN;
    }

    /** Formation pas encore terminée ni annulée */
    public function isActive(): bool
    {
        return in_array($this, [
            self::PLANNED,
            self::REGISTRATION_OPEN,
            self::REGISTRATION_CLOSED,
            self::IN_PROGRESS,
        ]);
    }

    /** Visible depuis le portail étudiant */
    public static function studentVisible(): array
    {
        return [
            self::REGISTRATION_OPEN,
            self::IN_PROGRESS,
            self::COMPLETED,
        ];
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases()
        );
    }
}