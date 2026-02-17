<?php

namespace App;

enum CertificateStatus: string
{
    case EMIS = 'emis';
    case REVOQUE = 'revoque';

    public function label(): string
    {
        return match ($this) {
            self::EMIS => 'Émis',
            self::REVOQUE => 'Révoqué',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EMIS => 'green',
            self::REVOQUE => 'red',
        };
    }

    public function isValid(): bool
    {
        return $this === self::EMIS;
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases()
        );
    }
}