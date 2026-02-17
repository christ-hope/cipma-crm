<?php

namespace App;

enum FormationMode: string
{
    case ONLINE     = 'online';
    case PRESENTIEL = 'presentiel';
    case HYBRID     = 'hybrid';

    public function label(): string
    {
        return match($this) {
            self::ONLINE     => 'En ligne',
            self::PRESENTIEL => 'Présentiel',
            self::HYBRID     => 'Hybride',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::ONLINE     => '💻',
            self::PRESENTIEL => '🏫',
            self::HYBRID     => '🔀',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'icon' => $c->icon()],
            self::cases()
        );
    }
}
