<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        $actionGroups = [
            'auth' => 'Authentication',
            'profile' => 'Profile',
            'scholarship' => 'Scholarship',
            'application' => 'Application',
            'document' => 'Document',
            'notification' => 'Notification',
            'settings' => 'Settings',
            'consent' => 'Consent',
            'matching' => 'Matching',
        ];

        return view('admin.audit.index', compact('logs', 'actionGroups'));
    }
}
