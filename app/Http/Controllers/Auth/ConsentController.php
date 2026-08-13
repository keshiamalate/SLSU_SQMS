<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ConsentRecord;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsentController extends Controller
{
    public function show() {
        $user = Auth::user();

        if ($user->hasValidConsent()) {
            return $this->redirectAfterConsent($user);
        }

        $consentVersion = SystemSetting::getValue('consent_version', '1.0');
        return view('auth.consent', compact('consentVersion'));
    }

    public function store(Request $request) {
        $request->validate([
            'consent' => ['required', 'accepted'],
        ], [
            'consent.required' => 'You must accept the Data Privacy Terms to continue.',
            'consent.accepted' => 'You must accept the Data Privacy Terms to continue.',
        ]);

        $user = Auth::user();
        $consentVersion = SystemSetting::getValue('consent_version', '1.0');

        ConsentRecord::create([
            'user_id' => $user->id,
            'consent_version' => $consentVersion,
            'consented' => 1,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        AuditLog::record('consent.signed', null, null, ['version' => $consentVersion]);

        return $this->redirectAfterConsent($user);
    }

    private function redirectAfterConsent($user) {
        $profile = $user->studentProfile;

        if (!$profile || !$profile->isComplete()) {
            return redirect()->route('student.questionnaire.show', ['step' => 1])
                ->with('success', 'Thank you! Please complete your profile to see matched scholarships.');
        }

        return redirect()->route('student.dashboard');
    }
}
