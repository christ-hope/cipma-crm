<?php

namespace App;
enum DocumentType: string
{
    case CV         = 'cv';
    case DIPLOME    = 'diplome';
    case PHOTO      = 'photo';
    case MOTIVATION = 'motivation';
    case ID_CARD    = 'id_card';
    case TRANSCRIPT = 'transcript';
    case OTHER      = 'other';

    public function label(): string
    {
        return match($this) {
            self::CV         => 'Curriculum Vitae',
            self::DIPLOME    => 'Diplôme',
            self::PHOTO      => 'Photo d\'identité',
            self::MOTIVATION => 'Lettre de motivation',
            self::ID_CARD    => 'Pièce d\'identité',
            self::TRANSCRIPT => 'Relevé de notes',
            self::OTHER      => 'Autre document',
        };
    }

    public function isRequired(): bool
    {
        return in_array($this, [self::CV, self::DIPLOME, self::PHOTO]);
    }

    /** Extensions acceptées par type */
    public function allowedMimeTypes(): array
    {
        return match($this) {
            self::PHOTO => ['image/jpeg', 'image/png', 'image/webp'],
            default     => ['application/pdf', 'image/jpeg', 'image/png'],
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => [
                'value'       => $c->value,
                'label'       => $c->label(),
                'is_required' => $c->isRequired(),
            ],
            self::cases()
        );
    }
}
