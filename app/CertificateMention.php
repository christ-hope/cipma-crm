<?php

namespace App;
enum CertificateMention: string
{
    case PASSABLE  = 'passable';
    case BIEN      = 'bien';
    case TRES_BIEN = 'tres_bien';
    case EXCELLENT = 'excellent';

    public function label(): string
    {
        return match($this) {
            self::PASSABLE  => 'Passable',
            self::BIEN      => 'Bien',
            self::TRES_BIEN => 'Très Bien',
            self::EXCELLENT => 'Excellent',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PASSABLE  => 'gray',
            self::BIEN      => 'blue',
            self::TRES_BIEN => 'purple',
            self::EXCELLENT => 'yellow',
        };
    }

    public function minNote(): float
    {
        return match($this) {
            self::PASSABLE  => 10.0,
            self::BIEN      => 12.0,
            self::TRES_BIEN => 14.0,
            self::EXCELLENT => 16.0,
        };
    }

    /** Détermine automatiquement la mention depuis une note /20 */
    public static function fromNote(float $note): self
    {
        return match(true) {
            $note >= 16.0 => self::EXCELLENT,
            $note >= 14.0 => self::TRES_BIEN,
            $note >= 12.0 => self::BIEN,
            default       => self::PASSABLE,
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => [
                'value'    => $c->value,
                'label'    => $c->label(),
                'min_note' => $c->minNote(),
                'color'    => $c->color(),
            ],
            self::cases()
        );
    }
}