<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false; // only created_at

    protected $fillable = [
        'user_id', 'action', 'module', 'description',
        'subject_type', 'subject_id',
        'old_values', 'new_values',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Record an audit log entry */
    public static function record(
        string $action,
        string $module,
        ?string $description = null,
        ?int $userId = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): self {
        return static::create([
            'user_id'      => $userId,
            'action'       => $action,
            'module'       => $module,
            'description'  => $description,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'old_values'   => $oldValues,
            'new_values'   => $newValues,
            'ip_address'   => request()?->ip(),
            'user_agent'   => request()?->userAgent(),
        ]);
    }
}
