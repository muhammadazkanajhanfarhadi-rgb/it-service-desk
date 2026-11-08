<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'description',
        'color', 'parent_id', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    /** Parent category (null = top-level) */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** Direct children (subcategories) */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /** Scope: only top-level categories */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /** Scope: active only */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
