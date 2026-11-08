<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'asset_number', 'name', 'brand', 'model', 'serial_number',
        'category_id', 'specifications', 'purchase_date', 'warranty_end',
        'status', 'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_end'  => 'date',
        'is_active'     => 'boolean',
    ];

    // Status constants
    const AVAILABLE   = 'available';
    const ASSIGNED    = 'assigned';
    const MAINTENANCE = 'maintenance';
    const RETIRED     = 'retired';

    /** Generate next asset number: AST-00001 */
    public static function generateAssetNumber(): string
    {
        $last = static::orderByDesc('id')->value('asset_number');
        $next = $last ? ((int) substr($last, 4)) + 1 : 1;
        return 'AST-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /* ── Relationships ─────────────────────────────────── */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class)->latest('assigned_at');
    }

    /** Current active assignment (returned_at is null) */
    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class)
            ->whereNull('returned_at')
            ->latest('assigned_at');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class)->latest('maintenance_date');
    }

    /* ── Helpers ───────────────────────────────────────── */

    public function isAvailable(): bool
    {
        return $this->status === self::AVAILABLE;
    }

    public function isAssigned(): bool
    {
        return $this->status === self::ASSIGNED;
    }

    public function isUnderMaintenance(): bool
    {
        return $this->status === self::MAINTENANCE;
    }

    public function isRetired(): bool
    {
        return $this->status === self::RETIRED;
    }

    /** Check if asset has an active assignment */
    public function hasActiveAssignment(): bool
    {
        return $this->currentAssignment()->exists();
    }

    /* ── Scopes ────────────────────────────────────────── */

    public function scopeAvailable($query)   { return $query->where('status', self::AVAILABLE); }
    public function scopeAssigned($query)    { return $query->where('status', self::ASSIGNED); }
    public function scopeMaintenance($query) { return $query->where('status', self::MAINTENANCE); }
    public function scopeRetired($query)     { return $query->where('status', self::RETIRED); }
    public function scopeActive($query)      { return $query->where('is_active', true); }
}
