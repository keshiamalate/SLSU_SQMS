<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\MlModel;
use App\Models\Scholarship;
use App\Models\User;
use App\Services\MlApiClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $admin = Auth::user()->load('role');

        $stats = [
            'total_students' => User::whereHas('role', fn($q) => $q->where('name', 'student'))->count(),
            'active_scholarships' => Scholarship::where('is_active', 1)->count(),
            'total_applications' => Application::count(),
            'qualified_students' => Application::whereIn('status', ['approved', 'matched', 'applied'])->distinct('user_id')->count(),
        ];

        $recentApplications = Application::with('user.studentProfile', 'scholarship.category')
            ->latest()
            ->take(5)
            ->get();

        $matchingStats = [
            'processed_today' => Application::whereDate('created_at', today())->count(),
            'total_matched' => Application::where('status', 'matched')->count(),
            'ml_active' => MlModel::where('is_active', 1)->exists(),
        ];

        $mlClient = app(MlApiClient::class);
        $mlActive = $mlClient->isAvailable();
        $mlInfo = $mlActive ? $mlClient->getModelInfo() : [];

        return view('admin.dashboard', compact(
            'admin',
            'stats',
            'recentApplications',
            'matchingStats',
            'mlActive',
            'mlInfo'
        ));
    }
}
