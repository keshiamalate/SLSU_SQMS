<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scholarship extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'code',
        'description',
        'funding_source',
        'monthly_allowance',
        'allowance_period',
        'benefit_type',
        'benefit_details',
        'allows_concurrent',
        'max_concurrent',
        'application_open_at',
        'application_close_at',
        'slots_available',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'allows_concurrent' => 'boolean',
            'is_active' => 'boolean',
            'application_open_at' => 'date',
            'application_close_at' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ScholarshipCategory::class, 'category_id');
    }

    public function criteria(): HasOne
    {
        return $this->hasOne(ScholarshipCriteria::class);
    }

    public function requiredDocuments(): HasMany
    {
        return $this->hasMany(ScholarshipRequiredDocument::class)->orderBy('display_order');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        $today = now()->toDateString();
        return $this->is_active
            && ($this->application_open_at === null || $this->application_open_at <= $today)
            && ($this->application_close_at === null || $this->application_close_at >= $today);
    }

    public function getFormattedAllowanceAttribute(): string
    {
        if (!$this->monthly_allowance) {
            return 'Non-cash benefit';
        }

        $amount = '₱' . number_format($this->monthly_allowance, 0);

        return match ($this->allowance_period) {
            'monthly' => $amount . ' / month',
            'per_semester' => $amount . ' / semester',
            'per_year' => $amount . ' / year',
            'one_time' => $amount . ' (one-time)',
            default => $amount,
        };
    }
}
