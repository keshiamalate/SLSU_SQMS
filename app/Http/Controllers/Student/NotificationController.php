<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRead;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifications = Notification::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)
                ->orWhere('is_mass', 1);
        })
            ->latest('created_at')
            ->paginate(15);

        // Mark all as read
        foreach ($notifications as $notif) {
            NotificationRead::firstOrCreate([
                'notification_id' => $notif->id,
                'user_id' => $user->id,
            ]);
        }

        return view('student.notifications.index', compact('notifications', 'user'));
    }

    public function unreadCount(): int
    {
        $user = Auth::user();

        return Notification::where(function ($q) use ($user) {
            $q->where('recipient_id', $user->id)
                ->orWhere('is_mass', 1);
        })
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $user->id))
            ->count();
    }
}
