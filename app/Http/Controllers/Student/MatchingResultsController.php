<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\MatchingService;
use Illuminate\Support\Facades\Auth;

class MatchingResultsController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('studentProfile', 'role');
        $profile = $user->studentProfile;
        $matcher = app(MatchingService::class);

        $matches = $matcher->getResultsForUser($user);

        $topMatches = $matches->where('match_label', 'top_match')->values();
        $goodMatches = $matches->where('match_label', 'good_match')->values();
        $possibleMatches = $matches->where('match_label', 'possible_match')->values();

        $unreadCount = Notification::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)->orWhere('is_mass', 1);
        })
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->count();

        return view('student.matching.index', compact(
            'user',
            'profile',
            'matches',
            'topMatches',
            'goodMatches',
            'possibleMatches',
            'unreadCount'
        ));
    }
}
