<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'avatar', 'is_active', 'customer_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEngineer(): bool
    {
        return $this->role === 'engineer';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * @return HasMany<InspectionRequest, $this>
     */
    public function createdInspectionRequests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class, 'requester_id');
    }

    /**
     * @return HasMany<InspectionRequest, $this>
     */
    public function assignedInspectionRequests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class, 'assignee_id');
    }

    /**
     * @return HasMany<FcmToken, $this>
     */
    public function fcmTokens(): HasMany
    {
        return $this->hasMany(FcmToken::class);
    }

    /**
     * Preferred channels for inspection notifications. Override per-user in
     * the future by reading from a user_preferences table; for now it's
     * the full fan-out for every role that cares.
     *
     * @return array<int, string>
     */
    public function notificationChannels(): array
    {
        if ($this->isCustomer()) {
            return [];
        }

        return ['database', 'mail', 'fcm', 'broadcast'];
    }
}
