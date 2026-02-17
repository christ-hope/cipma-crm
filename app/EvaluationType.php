<?php

namespace App;

enum EvaluationType: string
{
    case EXAM       = 'exam';
    case QUIZ       = 'quiz';
    case PROJECT    = 'project';
    case PRACTICAL  = 'practical';
    case ASSIGNMENT = 'assignment';

    public function label(): string
    {
        return match($this) {
            self::EXAM       => 'Examen',
            self::QUIZ       => 'Quiz',
            self::PROJECT    => 'Projet',
            self::PRACTICAL  => 'Travaux pratiques',
            self::ASSIGNMENT => 'Devoir',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::EXAM       => '📝',
            self::QUIZ       => '❓',
            self::PROJECT    => '🏗️',
            self::PRACTICAL  => '🔬',
            self::ASSIGNMENT => '📋',
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
