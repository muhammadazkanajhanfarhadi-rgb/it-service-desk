<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number', 'title', 'description', 'status',
        'requester_id', 'assignee_id', 'category_id', 'priority_id',
        'sla_policy_id', 'location_id',
        'impact', 'urgency',
        'response_due_at', 'resolution_due_at', 'first_response_at',
        'sla_response_breached', 'sla_resolution_breached',
        'resolution_notes', 'resolved_at', 'closed_at',
    ];

    protected $casts = [
        'response_due_at'        => 'datetime',
        'resolution_due_at'      => 'datetime',
        'first_response_at'      => 'datetime',
        'resolved_at'            => 'datetime',
        'closed_at'              => 'datetime',
        'sla_response_breached'  => 'boolean',
        'sla_resolution_breached'=> 'boolean',
    ];

    /* ── Status constants ──────────────────────────────── */
    const STATUS_NEW         = 'new';
    const STATUS_ASSIGNED    = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PENDING     = 'pending';
    const STATUS_RESOLVED    = 'resolved';
    const STATUS_CLOSED      = 'closed';
    const STATUS_REOPENED    = 'reopened';

    /** Allowed status transitions */
    const ALLOWED_TRANSITIONS = [
        'new'         => ['assigned'],
        'assigned'    => ['in_progress', 'pending'],
        'in_progress' => ['pending', 'resolved'],
        'pending'     => ['in_progress', 'assigned'],
        'resolved'    => ['closed', 'reopened'],
        'closed'      => ['reopened'],
        'reopened'    => ['assigned', 'in_progress'],
    ];

    /* ── Relationships ─────────────────────────────────── */

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function slaPolicy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TicketHistory::class)->orderBy('created_at');
    }

    /* ── Helpers ───────────────────────────────────────── */

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::ALLOWED_TRANSITIONS[$this->status] ?? []);
    }

    public function isOverdue(): bool
    {
        if (!$this->resolution_due_at) return false;
        if (in_array($this->status, ['resolved', 'closed'])) return false;
        return now()->isAfter($this->resolution_due_at);
    }

    public function slaStatusColor(): string
    {
        if ($this->sla_resolution_breached || $this->isOverdue()) return 'status-danger';
        if (!$this->resolution_due_at) return 'text-secondary';
        $minutesLeft = now()->diffInMinutes($this->resolution_due_at, false);
        if ($minutesLeft < 30) return 'status-warning';
        return 'status-success';
    }

    /** Generate next ticket number: INC-00001 */
    public static function generateTicketNumber(): string
    {
        $last = static::orderByDesc('id')->value('ticket_number');
        $next = $last ? ((int) substr($last, 4)) + 1 : 1;
        return 'INC-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /* ── Scopes ────────────────────────────────────────── */

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['closed']);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('requester_id', $user->id);
    }

    public function scopeAssignedTo($query, User $user)
    {
        return $query->where('assignee_id', $user->id);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('resolution_due_at')
                     ->where('resolution_due_at', '<', now())
                     ->whereNotIn('status', ['resolved', 'closed']);
    }
}
