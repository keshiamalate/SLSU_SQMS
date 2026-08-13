<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Scholarship;
use App\Models\ScholarshipCategory;
use App\Services\EligibilityEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScholarshipListingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->load('studentProfile', 'existingScholarships');
        $profile = $user->studentProfile;
        $engine = app(EligibilityEngine::class);

        $query = Scholarship::with('criteria', 'category')
            ->where('is_active', 1)
            ->latest();

        // Filter by category tab
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $scholarships = $query->get();
        $categories = ScholarshipCategory::all();

        // Tag each scholarship with eligibility status
        $scholarships = $scholarships->map(function ($scholarship) use ($profile, $engine) {
            $scholarship->is_eligible = $profile
                ? $engine->passes($profile, $scholarship)
                : null;
            $scholarship->fail_reason = ($profile && !$scholarship->is_eligible)
                ? $engine->failureReason($profile, $scholarship)
                : null;
            return $scholarship;
        });

        // Check if student already holds a scholarship (for banner)
        $hasExistingExclusive = $user->existingScholarships()
            ->where('is_exclusive', 1)
            ->first();

        $unreadCount = Notification::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)->orWhere('is_mass', 1);
        })
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->count();

        return view('student.scholarships.index', compact(
            'user',
            'profile',
            'scholarships',
            'categories',
            'hasExistingExclusive',
            'unreadCount'
        ));
    }
}
