<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birth_date',
        'address',
        'member_type',
        'qr_code',
        'role',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'birth_date' => 'date',
        ];
    }

    /**
     * Get the memberships for the user.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get the check-ins for the user.
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function userNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the active membership relation for the user.
     * This returns a relationship instance as Laravel expects.
     */
    public function activeMembership(): HasOne
    {
        return $this->hasOne(Membership::class)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest();
    }

    /**
     * Get the user's active membership model.
     * This actually retrieves the membership model.
     */
    public function getActiveMembership()
    {
        return $this->memberships()
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->first();
    }

    /**
     * Check if the user has an active membership.
     */
    public function hasActiveMembership(): bool
    {
        return $this->getActiveMembership() !== null;
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || ($this->role()->exists() && $this->role->name === 'admin');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName || ($this->role()->exists() && $this->role->name === $roleName);
    }

    /**
     * Generate a unique QR code for the user.
     * Chỉ admin mới có quyền tạo QR code mới.
     */
    public function generateQrCode(): ?string
    {
        // Kiểm tra nếu người gọi có vai trò admin
        if (auth()->check() && auth()->user()->isAdmin()) {
            $qrCode = md5($this->id . $this->email . time());
            $this->update(['qr_code' => $qrCode]);
            return $qrCode;
        }
        
        // Nếu không phải admin, trả về QR code hiện tại nếu có hoặc null
        return $this->qr_code;
    }
}
