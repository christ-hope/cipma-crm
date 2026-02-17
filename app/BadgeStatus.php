<?php

namespace App;

enum BadgeStatus: string
{
    case ACTIF = 'actif';
    case EXPIRE = 'expire';
    case REVOQUE = 'revoque';

    public function label(): string
    {
        return match ($this) {
            self::ACTIF => 'Actif',
            self::EXPIRE => 'Expiré',
            self::REVOQUE => 'Révoqué',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIF => 'green',
            self::EXPIRE => 'gray',
            self::REVOQUE => 'red',
        };
    }

    public function isValid(): bool
    {
        return $this === self::ACTIF;
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases()
        );
    }
}