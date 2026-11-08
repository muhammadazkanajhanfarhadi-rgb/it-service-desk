<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenance extends Model
{
    protected $table = 'asset_maintenance';

    protected $fillable = [
        'asset_id', 'type', 'description',
        'technician', 'performed_by',
        'maintenance_date', 'cost',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost'            => 'decimal:2',
    ];

    // Type constants
    const PREVENTIVE  = 'preventive';
    const CORRECTIVE  = 'corrective';
    const UPGRADE     = 'upgrade';
    const INSPECTION  = 'inspection';

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
