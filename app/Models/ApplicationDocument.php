<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\DocumentType;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    use HasUuid;

    protected $fillable = [
        'application_id',
        'type',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'type' => DocumentType::class,
    ];

    // ─── Relations ───────────────────────────────────────────────────────────
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $size = $this->file_size;
        if ($size < 1024)
            return "{$size} B";
        if ($size < 1048576)
            return round($size / 1024, 1) . ' KB';
        return round($size / 1048576, 1) . ' MB';
    }

}
