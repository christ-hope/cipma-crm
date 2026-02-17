<?php

namespace App;

enum EvaluationSource: string
{
    case CRM    = 'crm';
    case MOODLE = 'moodle';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match($this) {
            self::CRM    => 'CRM (formation interne)',
            self::MOODLE => 'Moodle',
            self::MANUAL => 'Saisie manuelle',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::CRM    => '🖥️',
            self::MOODLE => '📚',
            self::MANUAL => '✏️',
        };
    }

    /** Un evaluation_id est requis pour les évals CRM, nullable sinon */
    public function requiresEvaluationId(): bool
    {
        return $this === self::CRM;
    }

    /** La metadata Moodle doit être présente */
    public function requiresMoodleMetadata(): bool
    {
        return $this === self::MOODLE;
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'icon' => $c->icon()],
            self::cases()
        );
    }
}