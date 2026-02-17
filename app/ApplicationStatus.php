<?php

namespace App;

enum ApplicationStatus: string
{
    case SUBMITTED      = 'submitted';
    case UNDER_REVIEW   = 'under_review';
    case APPROVED       = 'approved';
    case REJECTED       = 'rejected';
    case INFO_REQUESTED = 'info_requested';

    public function label(): string
    {
        return match($this) {
            self::SUBMITTED      => 'Soumise',
            self::UNDER_REVIEW   => 'En révision',
            self::APPROVED       => 'Approuvée',
            self::REJECTED       => 'Rejetée',
            self::INFO_REQUESTED => 'Informations demandées',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SUBMITTED      => 'blue',
            self::UNDER_REVIEW   => 'yellow',
            self::APPROVED       => 'green',
            self::REJECTED       => 'red',
            self::INFO_REQUESTED => 'orange',
        };
    }

    /** Peut encore être traité par le responsable */
    public function isReviewable(): bool
    {
        return in_array($this, [
            self::SUBMITTED,
            self::UNDER_REVIEW,
            self::INFO_REQUESTED,
        ]);
    }

    /** Pour les <select> React */
    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label(), 'color' => $c->color()],
            self::cases()
        );
    }
}

