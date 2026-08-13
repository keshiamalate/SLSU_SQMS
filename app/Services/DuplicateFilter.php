<?php
namespace App\Services;

use App\Models\Scholarship;
use App\Models\StudentProfile;
use App\Models\DuplicateFilterLog;
use App\Models\User;

class DuplicateFilter
{
    /**
     * Returns true if the student is BLOCKED from this scholarship
     * due to a concurrent scholarship conflict.
     */
    public function isBlocked(User $user, Scholarship $scholarship): bool
    {
        // If the scholarship allows concurrent holding, never block
        if ($scholarship->allows_concurrent) {
            return false;
        }

        // Check if student has declared any exclusive existing scholarships
        $exclusiveExisting = $user->existingScholarships()
            ->where('is_exclusive', 1)
            ->exists();

        if ($exclusiveExisting) {
            $this->log($user, $scholarship, 'Has exclusive existing scholarship', 'blocked');
            return true;
        }

        // Check if student already has an approved application
        // for another non-concurrent scholarship
        $hasApprovedExclusive = $user->applications()
            ->where('status', 'approved')
            ->whereHas('scholarship', fn($q) => $q->where('allows_concurrent', 0))
            ->where('scholarship_id', '!=', $scholarship->id)
            ->exists();

        if ($hasApprovedExclusive) {
            $this->log($user, $scholarship, 'Already holds an exclusive scholarship', 'blocked');
            return true;
        }

        return false;
    }

    private function log(User $user, Scholarship $scholarship, string $reason, string $result): void
    {
        DuplicateFilterLog::create([
            'user_id' => $user->id,
            'scholarship_id' => $scholarship->id,
            'conflict_source' => $reason,
            'filter_result' => $result,
        ]);
    }
}
