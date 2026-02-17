<?php

namespace App;

enum EvaluationMode: string
{
    case CRM      = 'crm';
    case EXTERNAL = 'external';
    case MANUAL   = 'manual';

    public function label(): string
    {
        return match($this) {
            self::CRM      => 'Géré dans le CRM',
            self::EXTERNAL => 'Évaluation externe (Moodle)',
            self::MANUAL   => 'Saisie manuelle',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::CRM      => 'Évaluations détaillées créées et gérées dans le CRM',
            self::EXTERNAL => 'Note finale importée depuis Moodle uniquement',
            self::MANUAL   => 'Saisie manuelle complète (formation partenaire, présentiel externe)',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => [
                'value'       => $c->value,
                'label'       => $c->label(),
                'description' => $c->description(),
            ],
            self::cases()
        );
    }
}