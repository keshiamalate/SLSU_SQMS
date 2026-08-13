<?php
namespace App\Services;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchingService
{
    public function __construct(
        private EligibilityEngine $eligibility,
        private DuplicateFilter $duplicateFilter,
        private MatchingScorer $scorer,
        private MlApiClient $mlClient,
    ) {
    }

    public function runForUser(User $user): int
    {
        $profile = $user->studentProfile;

        if (!$profile || !$profile->isComplete()) {
            return 0;
        }

        $scholarships = Scholarship::with('criteria', 'category')
            ->where('is_active', 1)
            ->get();

        // Check if ML API is available
        $mlAvailable = $this->mlClient->isAvailable();

        // Pre-build feature vectors for batch ML prediction
        $eligibleScholarships = [];
        $batchRecords = [];

        foreach ($scholarships as $scholarship) {
            if (!$this->eligibility->passes($profile, $scholarship))
                continue;
            if ($this->duplicateFilter->isBlocked($user, $scholarship))
                continue;

            $eligibleScholarships[] = $scholarship;

            if ($mlAvailable) {
                $batchRecords[] = [
                    'scholarship_id' => $scholarship->id,
                    'gpa' => (float) $profile->cumulative_gpa,
                    'income_normalized' => min($profile->annual_family_income / 500000, 1.0),
                    'year_level' => $profile->year_level,
                    'course_match' => $this->scorer->courseMatchScore($profile, $scholarship),
                    'is_athlete' => (int) $profile->is_athlete,
                    'is_student_leader' => (int) $profile->is_student_leader,
                    'is_pwd' => (int) $profile->is_pwd,
                    'is_ip' => (int) $profile->is_indigenous_people,
                    'is_4ps' => (int) $profile->is_4ps_beneficiary,
                    'has_existing' => $user->existingScholarships()->where('is_exclusive', 1)->exists() ? 1 : 0,
                ];
            }
        }

        // Batch ML predictions
        $mlProbabilities = $mlAvailable && !empty($batchRecords)
            ? $this->mlClient->predictBatch($batchRecords)
            : [];

        $matched = 0;

        DB::beginTransaction();
        try {
            foreach ($eligibleScholarships as $scholarship) {
                $scores = $this->scorer->calculate($profile, $scholarship);
                $mlProb = $mlProbabilities[$scholarship->id] ?? null;

                // Final score: weighted score + ML probability (if available)
                $finalScore = $mlProb !== null
                    ? round(($scores['weighted_score'] * 0.6) + ($mlProb * 0.4), 4)
                    : $scores['weighted_score'];

                $existing = Application::where('user_id', $user->id)
                    ->where('scholarship_id', $scholarship->id)
                    ->first();

                if ($existing) {
                    // Only update scores — never touch status of an existing application
                    $existing->update([
                        'academic_score' => $scores['academic_score'],
                        'financial_score' => $scores['financial_score'],
                        'course_score' => $scores['course_score'],
                        'year_level_score' => $scores['year_level_score'],
                        'special_qual_score' => $scores['special_qual_score'],
                        'weighted_score' => $scores['weighted_score'],
                        'ml_probability' => $mlProb,
                        'final_score' => $finalScore,
                        'match_label' => $this->resolveLabel($finalScore),
                        // status intentionally NOT updated — preserves applied/approved/rejected
                    ]);
                } else {
                    // Brand new match — create with status matched
                    Application::create([
                        'user_id' => $user->id,
                        'scholarship_id' => $scholarship->id,
                        'academic_score' => $scores['academic_score'],
                        'financial_score' => $scores['financial_score'],
                        'course_score' => $scores['course_score'],
                        'year_level_score' => $scores['year_level_score'],
                        'special_qual_score' => $scores['special_qual_score'],
                        'weighted_score' => $scores['weighted_score'],
                        'ml_probability' => $mlProb,
                        'final_score' => $finalScore,
                        'match_label' => $this->resolveLabel($finalScore),
                        'status' => 'matched',
                    ]);
                }

                $matched++;
            }

            DB::commit();

            AuditLog::record(
                'matching.completed',
                null,
                null,
                [
                    'matched_count' => $matched,
                    'ml_used' => $mlAvailable,
                ],
                "Matching completed for user {$user->id} — {$matched} matched. ML: " . ($mlAvailable ? 'yes' : 'no')
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Matching failed for user {$user->id}: " . $e->getMessage());
            throw $e;
        }

        return $matched;
    }

    public function getResultsForUser(User $user): Collection
    {
        return Application::with('scholarship.category')
            ->where('user_id', $user->id)
            ->whereIn('status', ['matched', 'applied', 'under_review', 'approved'])
            ->orderByDesc('final_score')
            ->get();
    }

    private function resolveLabel(float $score): string
    {
        if ($score >= 0.80)
            return 'top_match';
        if ($score >= 0.60)
            return 'good_match';
        return 'possible_match';
    }
}
