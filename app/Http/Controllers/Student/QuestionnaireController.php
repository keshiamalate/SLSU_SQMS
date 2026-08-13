<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\StudentExistingScholarship;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MatchingService;

class QuestionnaireController extends Controller
{
    private const STEPS = [1, 2, 3, 4];

    public function show(int $step = 1)
    {
        if (!in_array($step, self::STEPS)) {
            return redirect()->route('student.questionnaire.show', ['step' => 1]);
        }

        $user = Auth::user();
        $saved = session("questionnaire.step_{$step}", []);

        return view("student.questionnaire.step_{$step}", compact('user', 'step', 'saved'));
    }

    public function store(Request $request, int $step)
    {
        $request->validate($this->rulesForStep($step), $this->messagesForStep($step));

        session(["questionnaire.step_{$step}" => $request->except(['_token', '_method'])]);

        if ($step < 4) {
            return redirect()->route('student.questionnaire.show', ['step' => $step + 1]);
        }

        // Step 4 submitted — save profile then run matching
        $this->saveProfile();

        // Run the matching pipeline
        $user = Auth::user()->fresh();
        $matcher = app(MatchingService::class);
        $count = $matcher->runForUser($user);

        return redirect()->route('student.dashboard')
            ->with('success', "Profile complete! We found {$count} scholarship(s) that match your profile.");
    }

    private function saveProfile(): void
    {
        $user = Auth::user();

        $step1 = session('questionnaire.step_1', []);
        $step2 = session('questionnaire.step_2', []);
        $step3 = session('questionnaire.step_3', []);
        $step4 = session('questionnaire.step_4', []);

        $income = (float) ($step2['annual_family_income'] ?? 0);
        $bracket = StudentProfile::resolveBracket($income);

        $profile = StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                // Step 1 — Academic
                'course' => $step1['course'],
                'year_level' => $step1['year_level'],
                'semester' => $step1['semester'],
                'cumulative_gpa' => $step1['cumulative_gpa'],
                'has_failing_grade' => isset($step1['has_failing_grade']) ? 1 : 0,
                'academic_honors' => $step1['academic_honors'] ?? null,
                'enrollment_status' => $step1['enrollment_status'] ?? 'regular',

                // Step 2 — Financial
                'annual_family_income_enc' => \Illuminate\Support\Facades\Crypt::encryptString((string) $income),
                'number_of_dependents' => $step2['number_of_dependents'] ?? 0,
                'is_4ps_beneficiary' => isset($step2['is_4ps_beneficiary']) ? 1 : 0,
                'income_bracket' => $bracket,

                // Step 3 — Personal
                'province_of_residence' => $step3['province_of_residence'],
                'municipality_of_residence' => $step3['municipality_of_residence'],
                'is_slsu_resident' => isset($step3['is_slsu_resident']) ? 1 : 0,

                // Step 4 — Qualifications
                'is_athlete' => isset($step4['is_athlete']) ? 1 : 0,
                'is_student_leader' => isset($step4['is_student_leader']) ? 1 : 0,
                'is_pwd' => isset($step4['is_pwd']) ? 1 : 0,
                'is_indigenous_people' => isset($step4['is_indigenous_people']) ? 1 : 0,

                'profile_completed_at' => now(),
            ]
        );

        // Save declared existing scholarships
        if (!empty($step4['existing_scholarships'])) {
            foreach ($step4['existing_scholarships'] as $existing) {
                if (!empty($existing['name'])) {
                    StudentExistingScholarship::create([
                        'user_id' => $user->id,
                        'scholarship_name' => $existing['name'],
                        'granting_body' => $existing['granting_body'] ?? null,
                        'is_exclusive' => isset($existing['is_exclusive']) ? 1 : 0,
                    ]);
                }
            }
        }

        // Clear session data
        for ($i = 1; $i <= 4; $i++) {
            session()->forget("questionnaire.step_{$i}");
        }

        AuditLog::record('profile.completed', $profile, null, null, 'Student completed profile questionnaire');
    }

    private function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'course' => 'required|string|max:150',
                'year_level' => 'required|integer|between:1,5',
                'semester' => 'required|integer|between:1,2',
                'cumulative_gpa' => 'required|numeric|between:1.00,3.00',
                'enrollment_status' => 'required|in:regular,irregular,transferee',
                'has_failing_grade' => 'nullable|boolean',
                'academic_honors' => 'nullable|string|max:100',
            ],
            2 => [
                'annual_family_income' => 'required|numeric|min:0',
                'number_of_dependents' => 'required|integer|min:0|max:20',
                'is_4ps_beneficiary' => 'nullable|boolean',
            ],
            3 => [
                'province_of_residence' => 'required|string|max:100',
                'municipality_of_residence' => 'required|string|max:100',
                'is_slsu_resident' => 'nullable|boolean',
            ],
            4 => [
                'existing_scholarships.*.name' => 'nullable|string|max:200',
            ],
            default => [],
        };
    }

    private function messagesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'course.required' => 'Please enter your degree/course.',
                'year_level.required' => 'Please select your year level.',
                'semester.required' => 'Please select your current semester.',
                'cumulative_gpa.required' => 'Please enter your current GPA.',
                'cumulative_gpa.between' => 'GPA must be between 1.00 (highest) and 3.00 (lowest passing).',
            ],
            2 => [
                'annual_family_income.required' => 'Please enter your annual family income.',
                'number_of_dependents.required' => 'Please enter number of dependents.',
            ],
            3 => [
                'province_of_residence.required' => 'Please enter your province.',
                'municipality_of_residence.required' => 'Please enter your municipality.',
            ],
            default => [],
        };
    }
}
