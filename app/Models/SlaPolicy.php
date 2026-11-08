<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaPolicy extends Model
{
    protected $fillable = [
        'name', 'priority_id',
        'response_minutes', 'resolution_minutes',
        'business_hours_only', 'business_start', 'business_end',
        'is_active', 'description',
    ];

    protected $casts = [
        'business_hours_only' => 'boolean',
        'is_active'           => 'boolean',
    ];

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    /** Human-readable response target, e.g. "15 menit" or "2 jam" */
    public function responseLabel(): string
    {
        return $this->minutesToLabel($this->response_minutes);
    }

    /** Human-readable resolution target */
    public function resolutionLabel(): string
    {
        return $this->minutesToLabel($this->resolution_minutes);
    }

    private function minutesToLabel(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        }
        $hours = $minutes / 60;
        return $hours == (int) $hours
            ? (int) $hours . ' jam'
            : number_format($hours, 1) . ' jam';
    }
}
