<?php

namespace App;

enum EnrollmentStatus: string
{
    case PENDING   = 'pending';
    case ACTIVE    = 'active';
    case COMPLETED = 'completed';
    case WITHDRAWN = 'withdrawn';
    case FAILED    = 'failed';

    public function label(): string
    {
        return match($this) {
            self::PENDING   => 'En attente de paiement',
            self::ACTIVE    => 'Actif',
            self::COMPLETED => 'Terminé',
            self::WITHDRAWN => 'Abandon',
            self::FAILED    => 'Échec',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING   => 'yellow',
            self::ACTIVE    => 'green',
            self::COMPLETED => 'blue',
            self::WITHDRAWN => 'orange',
            self::FAILED    => 'red',
        };
    }

    /** Peut recevoir des évaluations */
    public function canReceiveEvaluations(): bool
    {
        return in_array($this, [self::ACTIVE, self::COMPLETED]);
    }

    /** Statut final (ne peut plus évoluer automatiquement) */
    public function isFinished(): bool
    {
        return in_array($this, [self::COMPLETED, self::WITHDRAWN, self::FAILED]);
    }

    /** Peut demander un certificat */
    public function canRequestCertificate(): bool
    {
        return $this === self::COMPLETED;
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases()
        );
    }
}