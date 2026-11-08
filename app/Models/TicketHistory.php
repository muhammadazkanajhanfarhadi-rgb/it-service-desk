<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketHistory extends Model
{
    public $timestamps = false; // only created_at

    protected $fillable = [
        'ticket_id', 'user_id', 'action',
        'field_changed', 'old_value', 'new_value', 'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Action constants
    const ACTION_CREATED          = 'created';
    const ACTION_STATUS_CHANGED   = 'status_changed';
    const ACTION_ASSIGNED         = 'assigned';
    const ACTION_PRIORITY_CHANGED = 'priority_changed';
    const ACTION_COMMENT_ADDED    = 'comment_added';
    const ACTION_ATTACHMENT_ADDED = 'attachment_added';
    const ACTION_RESOLVED         = 'resolved';
    const ACTION_CLOSED           = 'closed';
    const ACTION_REOPENED         = 'reopened';

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Icon for this action in the timeline */
    public function icon(): string
    {
        return match($this->action) {
            self::ACTION_CREATED          => 'add_circle',
            self::ACTION_STATUS_CHANGED   => 'swap_horiz',
            self::ACTION_ASSIGNED         => 'assignment_ind',
            self::ACTION_PRIORITY_CHANGED => 'priority_high',
            self::ACTION_COMMENT_ADDED    => 'chat',
            self::ACTION_ATTACHMENT_ADDED => 'attach_file',
            self::ACTION_RESOLVED         => 'check_circle',
            self::ACTION_CLOSED           => 'lock',
            self::ACTION_REOPENED         => 'refresh',
            default                       => 'info',
        };
    }
}
