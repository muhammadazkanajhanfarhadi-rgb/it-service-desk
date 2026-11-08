<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
        'location_id',
        'phone',
        'avatar',
        'job_title',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    /* ── Relationships ─────────────────────────────────── */

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /* ── Role Helper Methods ───────────────────────────── */

    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function isEmployee(): bool   { return $this->hasRole(Role::EMPLOYEE);   }
    public function isTechnician(): bool { return $this->hasRole(Role::TECHNICIAN); }
    public function isManager(): bool    { return $this->hasRole(Role::MANAGER);    }
    public function isAdmin(): bool      { return $this->hasRole(Role::ADMIN);      }
    public function isApprover(): bool   { return $this->hasRole(Role::APPROVER);   }

    /** Admin or Manager can access management features */
    public function canManage(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    /* ── Avatar ────────────────────────────────────────── */

    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Fallback: initials-based avatar via UI Avatars
        $initials = collect(explode(' ', $this->name))
            ->map(fn($w) => strtoupper($w[0]))
            ->take(2)
            ->implode('');
        return "https://ui-avatars.com/api/?name={$initials}&background=2563eb&color=fff&size=64";
    }
}
