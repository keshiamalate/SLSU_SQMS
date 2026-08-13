<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipCategory;
use App\Models\ScholarshipCriteria;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $query = Scholarship::with('category')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('gpa')) {
            $query->whereHas('criteria', function ($q) use ($request) {
                $q->where('min_gpa', '<=', $request->gpa);
            });
        }

        $scholarships = $query->paginate(10)->withQueryString();
        $categories = ScholarshipCategory::all();

        return view('admin.scholarships.index', compact('scholarships', 'categories'));
    }

    public function create()
    {
        $categories = ScholarshipCategory::all();
        return view('admin.scholarships.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:scholarships,code',
            'category_id' => 'required|exists:scholarship_categories,id',
            'benefit_type' => 'required|in:cash,tuition_waiver,both,other',
            'description' => 'nullable|string',
            'funding_source' => 'nullable|string|max:200',
            'monthly_allowance' => 'nullable|numeric|min:0',
            'allowance_period' => 'required|in:monthly,per_semester,per_year,one_time',
            'allows_concurrent' => 'nullable|boolean',
            'slots_available' => 'nullable|integer|min:1',
            'application_open_at' => 'nullable|date',
            'application_close_at' => 'nullable|date|after_or_equal:application_open_at',
        ]);

        $scholarship = Scholarship::create([
            ...$request->only([
                'name',
                'code',
                'category_id',
                'description',
                'funding_source',
                'monthly_allowance',
                'allowance_period',
                'benefit_type',
                'benefit_details',
                'slots_available',
                'application_open_at',
                'application_close_at',
            ]),
            'allows_concurrent' => $request->boolean('allows_concurrent'),
            'is_active' => 1,
            'created_by' => Auth::id(),
        ]);

        // Save criteria immediately on creation
        ScholarshipCriteria::create([
            'scholarship_id' => $scholarship->id,
            'min_gpa' => $request->min_gpa ?: null,
            'max_gpa' => $request->max_gpa ?: null,
            'no_failing_grade' => $request->boolean('no_failing_grade'),
            'required_year_levels' => $request->required_year_levels ?: null,
            'max_annual_income' => $request->max_annual_income ?: null,
            'requires_4ps' => $request->boolean('requires_4ps'),
            'requires_slsu_residency' => $request->boolean('requires_slsu_residency'),
            'requires_athlete' => $request->boolean('requires_athlete'),
            'requires_student_leader' => $request->boolean('requires_student_leader'),
            'requires_pwd' => $request->boolean('requires_pwd'),
            'requires_indigenous_people' => $request->boolean('requires_indigenous_people'),
            'requires_philippine_citizenship' => $request->boolean('requires_philippine_citizenship'),
            'requires_active_enrollment' => $request->boolean('requires_active_enrollment'),
            'additional_requirements' => $request->additional_requirements,
        ]);

        AuditLog::record('scholarship.created', $scholarship, null, $scholarship->toArray());

        return redirect()->route('admin.scholarships.index')
            ->with('success', "Scholarship \"{$scholarship->name}\" created successfully.");
    }

    public function edit(Scholarship $scholarship)
    {
        $categories = ScholarshipCategory::all();
        $criteria = $scholarship->criteria;
        return view('admin.scholarships.edit', compact('scholarship', 'categories', 'criteria'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:scholarships,code,' . $scholarship->id,
            'category_id' => 'required|exists:scholarship_categories,id',
            'benefit_type' => 'required|in:cash,tuition_waiver,both,other',
            'description' => 'nullable|string',
            'funding_source' => 'nullable|string|max:200',
            'monthly_allowance' => 'nullable|numeric|min:0',
            'allowance_period' => 'required|in:monthly,per_semester,per_year,one_time',
            'slots_available' => 'nullable|integer|min:1',
            'application_open_at' => 'nullable|date',
            'application_close_at' => 'nullable|date|after_or_equal:application_open_at',
        ]);

        $old = $scholarship->toArray();

        $scholarship->update([
            ...$request->only([
                'name',
                'code',
                'category_id',
                'description',
                'funding_source',
                'monthly_allowance',
                'allowance_period',
                'benefit_type',
                'benefit_details',
                'slots_available',
                'application_open_at',
                'application_close_at',
            ]),
            'allows_concurrent' => $request->boolean('allows_concurrent'),
        ]);

        AuditLog::record('scholarship.updated', $scholarship, $old, $scholarship->fresh()->toArray());

        return redirect()->route('admin.scholarships.index')
            ->with('success', "Scholarship \"{$scholarship->name}\" updated successfully.");
    }

    public function destroy(Scholarship $scholarship)
    {
        $name = $scholarship->name;
        AuditLog::record('scholarship.deleted', $scholarship, $scholarship->toArray());
        $scholarship->delete();

        return redirect()->route('admin.scholarships.index')
            ->with('success', "Scholarship \"{$name}\" deleted.");
    }

    public function toggleActive(Scholarship $scholarship)
    {
        $scholarship->update(['is_active' => !$scholarship->is_active]);
        AuditLog::record('scholarship.toggled', $scholarship, null, ['is_active' => $scholarship->is_active]);

        return back()->with('success', "Scholarship status updated.");
    }
}
