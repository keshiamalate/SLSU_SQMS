<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('studentProfile', 'role')
            ->whereHas('role', fn($q) => $q->where('name', 'student'))
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('institutional_id', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'complete') {
                $query->whereHas('studentProfile', fn($q) => $q->whereNotNull('profile_completed_at'));
            } elseif ($request->status === 'incomplete') {
                $query->whereDoesntHave('studentProfile')
                    ->orWhereHas('studentProfile', fn($q) => $q->whereNull('profile_completed_at'));
            }
        }

        $students = $query->paginate(15)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function show(User $student)
    {
        $student->load([
            'studentProfile',
            'consentRecords',
            'existingScholarships',
            'applications.scholarship.category',
            'role',
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'institutional_id' => 'required|string|max:30|unique:users,institutional_id',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $studentRole = Role::where('name', 'student')->first();

        $student = User::create([
            'role_id' => $studentRole->id,
            'institutional_id' => strtoupper($request->institutional_id),
            'email' => $request->email,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
            'is_active' => 1,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student account created successfully.');
    }

    public function toggleActive(User $student)
    {
        $student->update(['is_active' => !$student->is_active]);

        return back()->with('success', 'Student status updated.');
    }
}
