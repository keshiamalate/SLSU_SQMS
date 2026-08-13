<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipCriteria;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ScholarshipCriteriaController extends Controller
{
    public function update(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'min_gpa' => 'nullable|numeric|between:1.00,3.00',
            'max_gpa' => 'nullable|numeric|between:1.00,3.00',
            'max_annual_income' => 'nullable|numeric|min:0',
            'required_year_levels' => 'nullable|array',
            'required_courses' => 'nullable|array',
        ]);

        $old = $scholarship->criteria?->toArray();
        $criteria = ScholarshipCriteria::updateOrCreate(
            ['scholarship_id' => $scholarship->id],
            [
                'min_gpa' => $request->min_gpa,
                'max_gpa' => $request->max_gpa,
                'no_failing_grade' => $request->boolean('no_failing_grade'),
                'required_year_levels' => $request->required_year_levels,
                'required_courses' => $request->required_courses
                    ? array_filter(explode(',', $request->required_courses_raw ?? ''))
                    : null,
                'max_annual_income' => $request->max_annual_income,
                'requires_4ps' => $request->boolean('requires_4ps'),
                'requires_slsu_residency' => $request->boolean('requires_slsu_residency'),
                'requires_athlete' => $request->boolean('requires_athlete'),
                'requires_student_leader' => $request->boolean('requires_student_leader'),
                'requires_pwd' => $request->boolean('requires_pwd'),
                'requires_indigenous_people' => $request->boolean('requires_indigenous_people'),
                'requires_philippine_citizenship' => $request->boolean('requires_philippine_citizenship'),
                'requires_active_enrollment' => $request->boolean('requires_active_enrollment'),
                'additional_requirements' => $request->additional_requirements,
            ]
        );

        AuditLog::record('criteria.updated', $criteria, $old, $criteria->toArray());

        return back()->with('success', 'Eligibility criteria saved successfully.');
    }
}
