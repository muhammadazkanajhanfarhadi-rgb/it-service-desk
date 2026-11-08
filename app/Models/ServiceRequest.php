<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'request_number', 'service_id', 'user_id', 'status',
        'approved_by', 'description', 'request_data',
        'approval_notes', 'approved_at', 'rejected_at', 'completed_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const PENDING_APPROVAL = 'pending_approval';
    const APPROVED          = 'approved';
    const REJECTED          = 'rejected';
    const IN_PROGRESS       = 'in_progress';
    const COMPLETED         = 'completed';
    const CANCELLED         = 'cancelled';

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** Generate next request number: REQ-00001 */
    public static function generateRequestNumber(): string
    {
        $last = static::orderByDesc('id')->value('request_number');
        $next = $last ? ((int) substr($last, 4)) + 1 : 1;
        return 'REQ-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function isPendingApproval(): bool
    {
        return $this->status === self::PENDING_APPROVAL;
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', self::PENDING_APPROVAL);
    }
}
