<?php

namespace App;

enum StudentStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case GRADUATED = 'graduated';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::SUSPENDED => 'Suspendu',
            self::GRADUATED => 'Diplômé',
            self::WITHDRAWN => 'Retiré',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::SUSPENDED => 'yellow',
            self::GRADUATED => 'blue',
            self::WITHDRAWN => 'red',
        };
    }

    public function canEnroll(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canRequestCertificate(): bool
    {
        return in_array($this, [self::ACTIVE, self::GRADUATED]);
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases()
        );
    }
}

