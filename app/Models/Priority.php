<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Priority extends Model
{
    protected $fillable = ['name', 'slug', 'level', 'color', 'icon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // Level constants (lower = more critical)
    const CRITICAL = 1;
    const HIGH     = 2;
    const MEDIUM   = 3;
    const LOW      = 4;

    public function slaPolicy(): HasOne
    {
        return $this->hasOne(SlaPolicy::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('level');
    }
}
