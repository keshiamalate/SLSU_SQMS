<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\StudentProfile;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index()
    {
        // ── Application status breakdown ───────────────────────────
        $statusBreakdown = Application::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Applications per scholarship ───────────────────────────
        $perScholarship = Scholarship::with('applications')
            ->where('is_active', 1)
            ->get()
            ->map(fn($s) => [
                'name' => $s->name,
                'total' => $s->applications->count(),
                'approved' => $s->applications->where('status', 'approved')->count(),
                'matched' => $s->applications->where('status', 'matched')->count(),
            ])
            ->sortByDesc('total')
            ->values();

        // ── Applications per category ──────────────────────────────
        $perCategory = Application::with('scholarship.category')
            ->get()
            ->groupBy('scholarship.category.name')
            ->map(fn($apps) => $apps->count())
            ->sortDesc();

        // ── Income bracket distribution ────────────────────────────
        $incomeBrackets = StudentProfile::selectRaw('income_bracket, COUNT(*) as count')
            ->groupBy('income_bracket')
            ->pluck('count', 'income_bracket')
            ->toArray();

        // ── Match label distribution ───────────────────────────────
        $matchLabels = Application::selectRaw('match_label, COUNT(*) as count')
            ->whereNotNull('match_label')
            ->groupBy('match_label')
            ->pluck('count', 'match_label')
            ->toArray();

        // ── Monthly application trend (last 6 months) ─────────────
        $monthlyTrend = Application::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // ── Summary stats ──────────────────────────────────────────
        $summary = [
            'total_students' => User::whereHas('role', fn($q) => $q->where('name', 'student'))->count(),
            'profile_complete' => StudentProfile::whereNotNull('profile_completed_at')->count(),
            'total_applications' => Application::count(),
            'approved' => Application::where('status', 'approved')->count(),
            'active_scholarships' => Scholarship::where('is_active', 1)->count(),
            'avg_score' => round(Application::whereNotNull('final_score')->avg('final_score') * 100, 1),
        ];

        return view('admin.analytics.index', compact(
            'statusBreakdown',
            'perScholarship',
            'perCategory',
            'incomeBrackets',
            'matchLabels',
            'monthlyTrend',
            'summary'
        ));
    }

    public function exportApplications(): StreamedResponse
    {
        $applications = Application::with('user.studentProfile', 'scholarship.category')
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="applications_' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($applications) {
            $handle = fopen('php://output', 'w');

            // CSV headers
            fputcsv($handle, [
                'Student Name',
                'Institutional ID',
                'Course',
                'Year Level',
                'GPA',
                'Income Bracket',
                'Scholarship',
                'Category',
                'Match Label',
                'Score (%)',
                'Status',
                'Applied At',
            ]);

            foreach ($applications as $app) {
                fputcsv($handle, [
                    $app->user->full_name,
                    $app->user->institutional_id,
                    $app->user->studentProfile?->course ?? '—',
                    $app->user->studentProfile?->year_level ?? '—',
                    $app->user->studentProfile?->cumulative_gpa ?? '—',
                    $app->user->studentProfile?->income_bracket ?? '—',
                    $app->scholarship->name,
                    $app->scholarship->category->name,
                    ucfirst(str_replace('_', ' ', $app->match_label ?? '')),
                    round(($app->final_score ?? 0) * 100) . '%',
                    ucfirst(str_replace('_', ' ', $app->status)),
                    $app->applied_at?->format('Y-m-d') ?? '—',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportStudents(): StreamedResponse
    {
        $students = User::with('studentProfile', 'applications')
            ->whereHas('role', fn($q) => $q->where('name', 'student'))
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($students) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Full Name',
                'Institutional ID',
                'Email',
                'Course',
                'Year Level',
                'GPA',
                'Income Bracket',
                'Municipality',
                'Province',
                'Profile Complete',
                'Total Applications',
                'Registered At',
            ]);

            foreach ($students as $student) {
                $profile = $student->studentProfile;
                fputcsv($handle, [
                    $student->full_name,
                    $student->institutional_id,
                    $student->email,
                    $profile?->course ?? '—',
                    $profile?->year_level ?? '—',
                    $profile?->cumulative_gpa ?? '—',
                    $profile?->income_bracket ?? '—',
                    $profile?->municipality_of_residence ?? '—',
                    $profile?->province_of_residence ?? '—',
                    $profile?->isComplete() ? 'Yes' : 'No',
                    $student->applications->count(),
                    $student->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportAll(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="smartmatch_export_' . now()->format('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // ── Summary header ─────────────────────────────────
            fputcsv($handle, ['SmartMatch Full Export — ' . now()->format('F d, Y h:i A')]);
            fputcsv($handle, ['Generated by', auth()->user()->full_name]);
            fputcsv($handle, []);

            // ── Applications ───────────────────────────────────
            fputcsv($handle, ['=== APPLICATIONS ===']);
            fputcsv($handle, [
                'Student Name',
                'Institutional ID',
                'Email',
                'Course',
                'Year Level',
                'GPA',
                'Income Bracket',
                'Scholarship',
                'Category',
                'Allowance',
                'Match Label',
                'Score (%)',
                'ML Probability (%)',
                'Status',
                'Applied At',
                'Decision Notes',
            ]);

            $applications = Application::with(
                'user.studentProfile',
                'scholarship.category'
            )->latest()->get();

            foreach ($applications as $app) {
                fputcsv($handle, [
                    $app->user->full_name,
                    $app->user->institutional_id,
                    $app->user->email,
                    $app->user->studentProfile?->course ?? '—',
                    $app->user->studentProfile?->year_level ?? '—',
                    $app->user->studentProfile?->cumulative_gpa ?? '—',
                    $app->user->studentProfile?->income_bracket ?? '—',
                    $app->scholarship->name,
                    $app->scholarship->category->name,
                    $app->scholarship->formatted_allowance,
                    ucfirst(str_replace('_', ' ', $app->match_label ?? '')),
                    round(($app->final_score ?? 0) * 100) . '%',
                    $app->ml_probability ? round($app->ml_probability * 100) . '%' : '—',
                    ucfirst(str_replace('_', ' ', $app->status)),
                    $app->applied_at?->format('Y-m-d') ?? '—',
                    $app->decision_notes ?? '—',
                ]);
            }

            fputcsv($handle, []);

            // ── Students ───────────────────────────────────────
            fputcsv($handle, ['=== REGISTERED STUDENTS ===']);
            fputcsv($handle, [
                'Full Name',
                'Institutional ID',
                'Email',
                'Course',
                'Year Level',
                'GPA',
                'Income Bracket',
                'Municipality',
                'Province',
                '4Ps',
                'Athlete',
                'Student Leader',
                'PWD',
                'IP',
                'Profile Complete',
                'Registered At',
            ]);

            $students = User::with('studentProfile')
                ->whereHas('role', fn($q) => $q->where('name', 'student'))
                ->get();

            foreach ($students as $s) {
                $p = $s->studentProfile;
                fputcsv($handle, [
                    $s->full_name,
                    $s->institutional_id,
                    $s->email,
                    $p?->course ?? '—',
                    $p?->year_level ?? '—',
                    $p?->cumulative_gpa ?? '—',
                    $p?->income_bracket ?? '—',
                    $p?->municipality_of_residence ?? '—',
                    $p?->province_of_residence ?? '—',
                    $p?->is_4ps_beneficiary ? 'Yes' : 'No',
                    $p?->is_athlete ? 'Yes' : 'No',
                    $p?->is_student_leader ? 'Yes' : 'No',
                    $p?->is_pwd ? 'Yes' : 'No',
                    $p?->is_indigenous_people ? 'Yes' : 'No',
                    $p?->isComplete() ? 'Yes' : 'No',
                    $s->created_at->format('Y-m-d'),
                ]);
            }

            fputcsv($handle, []);

            // ── Scholarships ───────────────────────────────────
            fputcsv($handle, ['=== SCHOLARSHIPS ===']);
            fputcsv($handle, [
                'Name',
                'Code',
                'Category',
                'Funding Source',
                'Allowance',
                'GPA Required',
                'Max Income',
                'Open Date',
                'Close Date',
                'Slots',
                'Status',
            ]);

            $scholarships = Scholarship::with('category', 'criteria')->get();

            foreach ($scholarships as $s) {
                fputcsv($handle, [
                    $s->name,
                    $s->code,
                    $s->category->name,
                    $s->funding_source ?? '—',
                    $s->formatted_allowance,
                    $s->criteria?->min_gpa ?? 'None',
                    $s->criteria?->max_annual_income
                    ? '₱' . number_format($s->criteria->max_annual_income)
                    : 'None',
                    $s->application_open_at ?? '—',
                    $s->application_close_at ?? '—',
                    $s->slots_available ?? 'Unlimited',
                    $s->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
