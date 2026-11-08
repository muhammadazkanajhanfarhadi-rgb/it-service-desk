<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    // Slug constants for safe role checks
    const EMPLOYEE   = 'employee';
    const TECHNICIAN = 'technician';
    const MANAGER    = 'it-manager';
    const ADMIN      = 'admin';
    const APPROVER   = 'approver';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
