<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'phone', 'password', 'sex', 'nin', 'avatar', 'original_avatar'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasTenants, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, SoftDeletes, TwoFactorAuthenticatable;

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
            'auth' => AsArrayObject::class,
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function isAdmin(): bool
    {
        return $this->role_id == Role::ADMIN;
    }

    public function isStudent(): bool
    {
        return $this->role_id == Role::STUDENT;
    }

    public function isStaff(): bool
    {
        return $this->role_id == Role::STAFF;
    }

    public function isGuardian(): bool
    {
        return $this->role_id == Role::GUARDIAN;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        switch ($panel->getId()) {
            case 'admin':
                if (! $this->hasVerifiedEmail()) {
                    return $this->isAdmin();
                }

                return $this->isAdmin() && $this->hasVerifiedEmail();
            case 'student':
                return $this->isStudent();
            case 'staff':
                return $this->isStaff();
            case 'guardian':
                return $this->isGuardian();
            default:
                return false;
        }
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->schools;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->schools()->whereKey($tenant)->exists();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return null;
        // return Cache::remember("user-{$this->id}-avatar-url", now()->addHour(), function () {
        //     return $this->user_id
        //         ? Storage::url('logos/'.$this->query()->find($this->user_id)->load('setting')->setting->company_logo)
        //         : ($this->setting->company_logo ? Storage::url('logos/'.$this->setting->company_logo) : null);
        // });
    }

    // public function verified(): bool
    // {
    //     if (isset($this->user_id)) {
    //         return $this->query()->find($this->user_id)->load('setting')->setting->kyc === 'verified';
    //     }

    //     return $this->setting->kyc === 'verified';
    // }

    // public function getDefaultCountry(): string
    // {
    //     return $this->load('setting')->setting->sch_country;
    // }

    // public function getDefaultState(): string
    // {
    //     return $this->load('setting')->setting->sch_state;
    // }

    // Roles

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function guardian(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }
}
