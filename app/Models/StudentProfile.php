<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'course',
        'year_level',
        'semester',
        'cumulative_gpa',
        'has_failing_grade',
        'academic_honors',
        'enrollment_status',
        'annual_family_income_enc',
        'number_of_dependents',
        'is_4ps_beneficiary',
        'income_bracket',
        'province_of_residence',
        'municipality_of_residence',
        'is_slsu_resident',
        'is_athlete',
        'is_student_leader',
        'is_pwd',
        'is_indigenous_people',
        'profile_completed_at',
    ];
    protected $hidden = ['annual_family_income_enc'];
    protected function casts(): array
    {
        return [
            'has_failing_grade' => 'boolean',
            'is_4ps_beneficiary' => 'boolean',
            'is_slsu_resident' => 'boolean',
            'is_athlete' => 'boolean',
            'is_student_leader' => 'boolean',
            'is_pwd' => 'boolean',
            'is_indigenous_people' => 'boolean',
            'profile_completed_at' => 'datetime',
            'cumulative_gpa' => 'decimal:2',
        ];
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setAnnualFamilyIncomeAttribute(int|float $value): void
    {
        $this->attributes['annual_family_income_enc'] = Crypt::encryptString((string) $value);
    }
    public function getAnnualFamilyIncomeAttribute(): float
    {
        return (float) Crypt::decryptString($this->attributes['annual_family_income_enc']);
    }
    public static function resolveBracket(float $income): string
    {
        return match (true) {
            $income < 60000 => 'A',
            $income < 150000 => 'B',
            $income < 300000 => 'C',
            $income < 500000 => 'D',
            default => 'E',
        };
    }

    public static function bracketLabel(string $bracket): string
    {
        return match (strtoupper($bracket)) {
            'A' => 'Low Income',
            'B' => 'Lower-Middle',
            'C' => 'Middle Income',
            'D' => 'Upper-Middle',
            'E' => 'High Income',
            default => 'Unknown',
        };
    }

    public function isPassingGpa(): bool
    {
        return $this->cumulative_gpa <= 3.00;
    }

    public function meetsGpaRequirement(float $requiredGpa): bool
    {
        // In PH system: lower number = better grade
        // Student qualifies if their GPA is <= the required GPA
        return $this->cumulative_gpa <= $requiredGpa;
    }

    public function isComplete(): bool
    {
        return $this->profile_completed_at !== null;
    }
}
