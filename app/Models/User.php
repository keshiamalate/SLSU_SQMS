<?php
namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role_id',
        'institutional_id',
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'is_active',
        'mfa_secret',
        'mfa_enabled',
    ];
    protected $hidden = ['password', 'remember_token', 'mfa_secret'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'mfa_enabled' => 'boolean',
        ];
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }
    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
    public function existingScholarships(): HasMany
    {
        return $this->hasMany(StudentExistingScholarship::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role->name === $role;
    }
    public function isAdmin(): bool
    {
        return in_array($this->role->name, ['super_admin', 'scholarship_admin', 'verifier']);
    }
    public function isStudent(): bool
    {
        return $this->role->name === 'student';
    }

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name, $this->suffix]));
    }
    public function hasValidConsent(): bool
    {
        $version = SystemSetting::getValue('consent_version', '1.0');
        return $this->consentRecords()->where('consented', 1)->where('consent_version', $version)->exists();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }
}
