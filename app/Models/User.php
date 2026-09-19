<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'role', 'is_active', 'avatar', 'shop_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function shop(){ return $this->belongsTo(Shop::class); }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        $role = $this->role ?? 'admin';
        return in_array($role, ['owner', 'admin'], true);
    }

    public function isOwner(): bool
    {
        return ($this->role ?? 'admin') === 'owner';
    }

    public function isCashier(): bool
    {
        return ($this->role ?? 'admin') === 'cashier';
    }

    public function isAdministrator(): bool
    {
        return in_array($this->role ?? 'admin', ['owner', 'admin'], true);
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role ?? 'admin', (array) $roles, true);
    }

    public function hasPermission(string $permission): bool
    {
        if (in_array($this->role ?? 'admin', ['owner', 'admin'], true)) {
            return true;
        }
        // cashier limited permissions
        $cashierAllowed = ['create_sale','view_products','view_stock'];
        if ($this->role === 'cashier' && in_array($permission, $cashierAllowed, true)) return true;
        return false;
    }

    public function roleLabel(): string
    {
        return match ($this->role ?? 'admin') {
            'owner'       => 'Owner',
            'admin'       => 'Admin',
            'cashier'     => 'Cashier',
            default        => ucfirst((string) ($this->role ?? 'Admin')),
        };
    }
}
