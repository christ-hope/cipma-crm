<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\EvaluationMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormationType extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'requires_certification',
        'evaluation_mode',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'requires_certification' => 'boolean',
        'is_active' => 'boolean',
        'evaluation_mode' => EvaluationMode::class,

    ];

    // Relations
    public function formations()
    {
        return $this->hasMany(Formation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function isCrmEvaluation(): bool
    {
        return $this->evaluation_mode === 'crm';
    }

    public function isExternalEvaluation(): bool
    {
        return $this->evaluation_mode === 'external';
    }

    public function isManualEvaluation(): bool
    {
        return $this->evaluation_mode === 'manual';
    }
}
