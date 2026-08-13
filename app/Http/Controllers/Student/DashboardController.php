<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\MatchingService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('studentProfile', 'role');
        $profile = $user->studentProfile;

        $matches = collect();
        if ($profile && $profile->isComplete()) {
            $matcher = app(MatchingService::class);
            $matches = $matcher->getResultsForUser($user);
        }

        $topMatches = $matches->where('match_label', 'top_match');
        $goodMatches = $matches->where('match_label', 'good_match');
        $possibleMatches = $matches->where('match_label', 'possible_match');

        $unreadCount = \App\Models\Notification::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)
                ->orWhere('is_mass', 1);
        })
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->count();

        return view('student.dashboard', compact(
            'user',
            'profile',
            'matches',
            'topMatches',
            'goodMatches',
            'possibleMatches',
            'unreadCount'
        ));
    }

    public function rerunMatching()
    {
        $user = Auth::user()->load('studentProfile');
        $matcher = app(MatchingService::class);
        $count = $matcher->runForUser($user);

        return redirect()->route('student.matching.index')
            ->with('success', "Matching updated — {$count} scholarship(s) found.");
    }
}
