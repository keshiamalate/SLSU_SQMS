<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipCriteria extends Model
{
    protected $table = 'scholarship_criteria';
    protected $fillable = [
        'scholarship_id',
        'min_gpa',
        'max_gpa',
        'no_failing_grade',
        'required_year_levels',
        'required_courses',
        'required_honors',
        'max_annual_income',
        'requires_4ps',
        'required_income_brackets',
        'requires_slsu_residency',
        'required_municipalities',
        'requires_athlete',
        'requires_student_leader',
        'requires_pwd',
        'requires_indigenous_people',
        'requires_philippine_citizenship',
        'requires_active_enrollment',
        'additional_requirements',
    ];
    protected function casts(): array
    {
        return [
            'required_year_levels' => 'array',
            'required_courses' => 'array',
            'required_income_brackets' => 'array',
            'required_municipalities' => 'array',
            'no_failing_grade' => 'boolean',
            'requires_4ps' => 'boolean',
            'requires_slsu_residency' => 'boolean',
            'requires_athlete' => 'boolean',
            'requires_student_leader' => 'boolean',
            'requires_pwd' => 'boolean',
            'requires_indigenous_people' => 'boolean',
            'requires_philippine_citizenship' => 'boolean',
            'requires_active_enrollment' => 'boolean',
        ];
    }
    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }
}
