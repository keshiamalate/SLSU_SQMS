<?php
namespace App\Services;

use App\Models\Scholarship;
use App\Models\StudentProfile;

class MatchingScorer
{
    /**
     * Weights from the manuscript:
     * Academic Compatibility  30%
     * Financial Need          30%
     * Course Relevance        15%
     * Year Level Alignment    15%
     * Special Qualifications  10%
     */
    private const WEIGHTS = [
        'academic' => 0.30,
        'financial' => 0.30,
        'course' => 0.15,
        'year_level' => 0.15,
        'special' => 0.10,
    ];

    public function calculate(StudentProfile $profile, Scholarship $scholarship): array
    {
        $criteria = $scholarship->criteria;

        $academic = $this->academicScore($profile, $criteria);
        $financial = $this->financialScore($profile, $criteria);
        $course = $this->courseMatchScore($profile, $scholarship);
        $yearLevel = $this->yearLevelScore($profile, $criteria);
        $special = $this->specialScore($profile, $criteria);

        $weighted = round(
            ($academic * self::WEIGHTS['academic']) +
            ($financial * self::WEIGHTS['financial']) +
            ($course * self::WEIGHTS['course']) +
            ($yearLevel * self::WEIGHTS['year_level']) +
            ($special * self::WEIGHTS['special']),
            4
        );

        return [
            'academic_score' => round($academic, 4),
            'financial_score' => round($financial, 4),
            'course_score' => round($course, 4),
            'year_level_score' => round($yearLevel, 4),
            'special_qual_score' => round($special, 4),
            'weighted_score' => $weighted,
            'match_label' => $this->label($weighted),
        ];
    }

    // ── Score Components ──────────────────────────────────────────

    private function academicScore(StudentProfile $profile, $criteria): float
    {
        if (!$criteria || $criteria->min_gpa === null) {
            // No GPA requirement — full score if student is passing
            return $profile->cumulative_gpa <= 3.00 ? 1.0 : 0.0;
        }

        // PH grading: lower = better
        // Student is AT or ABOVE requirement — calculate how much better
        // e.g. requirement = 1.75, student GPA = 1.25 → exceeds by 0.50
        $required = $criteria->min_gpa;
        $gpa = $profile->cumulative_gpa;

        if ($gpa > $required) {
            return 0.0; // failed eligibility — should not reach here
        }

        // Score based on how far BELOW the threshold the student is
        // Range: 1.00 (perfect) to required GPA
        // Student at 1.00 = 1.0 score, student at exactly required = 0.5 score
        $range = $required - 1.00;
        if ($range <= 0)
            return 1.0;

        $score = 0.5 + (0.5 * (($required - $gpa) / $range));
        return min(1.0, max(0.0, round($score, 4)));
    }

    private function financialScore(StudentProfile $profile, $criteria): float
    {
        if (!$criteria || $criteria->max_annual_income === null) {
            // No income requirement — score based on bracket
            return match ($profile->income_bracket) {
                'A' => 1.00,
                'B' => 0.85,
                'C' => 0.65,
                'D' => 0.40,
                'E' => 0.20,
                default => 0.50,
            };
        }

        $income = $profile->annual_family_income;
        $limit = $criteria->max_annual_income;

        if ($income > $limit)
            return 0.0;

        // Lower income relative to limit = higher need = higher score
        $ratio = $income / $limit;
        return round(1.0 - ($ratio * 0.5), 4); // range: 0.5–1.0
    }

    public function courseMatchScore(StudentProfile $profile, Scholarship $scholarship): int
    {
        $criteria = $scholarship->criteria;
        if (!$criteria || empty($criteria->required_courses))
            return 1;

        foreach ($criteria->required_courses as $required) {
            if (stripos($profile->course, $required) !== false)
                return 1;
        }

        return 0;
    }

    private function yearLevelScore(StudentProfile $profile, $criteria): float
    {
        if (!$criteria || empty($criteria->required_year_levels)) {
            return 1.0; // no year level restriction
        }

        return in_array($profile->year_level, $criteria->required_year_levels) ? 1.0 : 0.0;
    }

    private function specialScore(StudentProfile $profile, $criteria): float
    {
        if (!$criteria)
            return 0.5;

        $flags = [
            'requires_athlete' => $profile->is_athlete,
            'requires_student_leader' => $profile->is_student_leader,
            'requires_pwd' => $profile->is_pwd,
            'requires_indigenous_people' => $profile->is_indigenous_people,
            'requires_4ps' => $profile->is_4ps_beneficiary,
            'requires_slsu_residency' => $profile->is_slsu_resident,
        ];

        $required = 0;
        $matched = 0;

        foreach ($flags as $criteriaField => $profileValue) {
            if ($criteria->$criteriaField) {
                $required++;
                if ($profileValue)
                    $matched++;
            }
        }

        if ($required === 0)
            return 0.5; // no special requirements — neutral score

        return round($matched / $required, 4);
    }

    // ── Match Label ───────────────────────────────────────────────

    private function label(float $score): string
    {
        if ($score >= 0.80)
            return 'top_match';
        if ($score >= 0.60)
            return 'good_match';
        return 'possible_match';
    }
}
