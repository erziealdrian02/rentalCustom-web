<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // =========================================================
    // FILLABLE — kolom yang boleh diisi massal
    // =========================================================
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    // =========================================================
    // HIDDEN — kolom yang disembunyikan saat serialisasi
    // =========================================================
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // =========================================================
    // CASTS — konversi tipe otomatis
    // =========================================================
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================
    // CONSTANTS — nilai enum role & status
    // =========================================================
    const ROLES = ['admin', 'manager', 'operator', 'driver'];

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_BANNED   = 'banned';

    // =========================================================
    // SCOPES — query helper
    // =========================================================

    /**
     * Hanya user yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Filter berdasarkan role
     * Contoh: User::role('driver')->get();
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    // =========================================================
    // ACCESSORS / HELPERS
    // =========================================================

    /**
     * Cek apakah user aktif
     */
    // public function isActive(): bool
    // {
    //     return $this->status === self::STATUS_ACTIVE;
    // }
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Cek apakah user memiliki role tertentu
     * Contoh: $user->hasRole('admin')
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Label role yang lebih ramah untuk ditampilkan
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'    => 'Administrator',
            'manager'  => 'Manager',
            'operator' => 'Operator',
            'driver'   => 'Driver',
            default    => ucfirst($this->role),
        };
    }

    /**
     * Label status dengan warna (untuk badge)
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'green',
            'inactive' => 'yellow',
            'banned'   => 'red',
            default    => 'gray',
        };
    }
}