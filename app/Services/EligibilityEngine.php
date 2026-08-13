<?php
namespace App\Services;

use App\Models\Scholarship;
use App\Models\StudentProfile;
use App\Models\ScholarshipCriteria;

class EligibilityEngine
{
    /**
     * Returns true if the student passes ALL hard eligibility rules
     * for the given scholarship.
     *
     * In PH grading: lower GPA number = better grade.
     * 1.0 = highest, 3.0 = lowest passing, above 3.0 = failing.
     */
    public function passes(StudentProfile $profile, Scholarship $scholarship): bool
    {
        $criteria = $scholarship->criteria;

        // No criteria configured yet — treat as open to all
        if (!$criteria) {
            return true;
        }

        // ── GPA Check ─────────────────────────────────────────────
        // criteria->min_gpa = the MAXIMUM GPA number allowed
        // e.g. min_gpa = 1.75 means student must have 1.75 or lower
        if ($criteria->min_gpa !== null) {
            if ($profile->cumulative_gpa > $criteria->min_gpa) {
                return false;
            }
        }

        // criteria->max_gpa = the LOWEST acceptable GPA
        // e.g. max_gpa = 3.00 means student must have 3.00 or lower
        if ($criteria->max_gpa !== null) {
            if ($profile->cumulative_gpa > $criteria->max_gpa) {
                return false;
            }
        }

        // ── No Failing Grade ──────────────────────────────────────
        if ($criteria->no_failing_grade && $profile->has_failing_grade) {
            return false;
        }

        // ── Year Level ────────────────────────────────────────────
        if (!empty($criteria->required_year_levels)) {
            if (!in_array($profile->year_level, $criteria->required_year_levels)) {
                return false;
            }
        }

        // ── Course ────────────────────────────────────────────────
        if (!empty($criteria->required_courses)) {
            $match = false;
            foreach ($criteria->required_courses as $requiredCourse) {
                if (stripos($profile->course, $requiredCourse) !== false) {
                    $match = true;
                    break;
                }
            }
            if (!$match)
                return false;
        }

        // ── Income ────────────────────────────────────────────────
        if ($criteria->max_annual_income !== null) {
            if ($profile->annual_family_income > $criteria->max_annual_income) {
                return false;
            }
        }

        // ── 4Ps ───────────────────────────────────────────────────
        if ($criteria->requires_4ps && !$profile->is_4ps_beneficiary) {
            return false;
        }

        // ── Residency ─────────────────────────────────────────────
        if ($criteria->requires_slsu_residency && !$profile->is_slsu_resident) {
            return false;
        }

        // ── Special Categories ────────────────────────────────────
        if ($criteria->requires_athlete && !$profile->is_athlete)
            return false;
        if ($criteria->requires_student_leader && !$profile->is_student_leader)
            return false;
        if ($criteria->requires_pwd && !$profile->is_pwd)
            return false;
        if ($criteria->requires_indigenous_people && !$profile->is_indigenous_people)
            return false;

        // ── Active Enrollment ─────────────────────────────────────
        if ($criteria->requires_active_enrollment) {
            if ($profile->enrollment_status === null)
                return false;
        }

        return true;
    }

    /**
     * Returns a human-readable reason why a student failed eligibility.
     * Useful for admin review and debugging.
     */
    public function failureReason(StudentProfile $profile, Scholarship $scholarship): string
    {
        $criteria = $scholarship->criteria;
        if (!$criteria)
            return 'No criteria configured.';

        if ($criteria->min_gpa !== null && $profile->cumulative_gpa > $criteria->min_gpa)
            return "GPA {$profile->cumulative_gpa} does not meet requirement of {$criteria->min_gpa} or better.";

        if ($criteria->no_failing_grade && $profile->has_failing_grade)
            return 'Student has a failing grade.';

        if (!empty($criteria->required_year_levels) && !in_array($profile->year_level, $criteria->required_year_levels))
            return "Year level {$profile->year_level} not in required levels: " . implode(', ', $criteria->required_year_levels);

        if ($criteria->max_annual_income !== null && $profile->annual_family_income > $criteria->max_annual_income)
            return 'Family income exceeds the scholarship limit.';

        if ($criteria->requires_4ps && !$profile->is_4ps_beneficiary)
            return 'Requires 4Ps beneficiary status.';

        if ($criteria->requires_slsu_residency && !$profile->is_slsu_resident)
            return 'Requires SLSU residency.';

        if ($criteria->requires_athlete && !$profile->is_athlete)
            return 'Requires varsity athlete status.';

        if ($criteria->requires_student_leader && !$profile->is_student_leader)
            return 'Requires student leader status.';

        if ($criteria->requires_pwd && !$profile->is_pwd)
            return 'Requires PWD status.';

        if ($criteria->requires_indigenous_people && !$profile->is_indigenous_people)
            return 'Requires Indigenous People (IP) status.';

        return 'Does not meet one or more eligibility requirements.';
    }
}
