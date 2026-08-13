<form method="POST" action="{{ route('admin.applications.status', $application) }}">
    @csrf @method('PATCH')

    <div class="mb-3">
        <label class="form-label" style="font-size:.85rem;font-weight:600;">Change Status To</label>
        <select name="status" class="form-select form-select-sm">
            @foreach([
                    'applied' => 'Applied',
                    'under_review' => 'Under Review',
                    'documents_pending' => 'Documents Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ $application->status === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label" style="font-size:.85rem;font-weight:600;">Decision Notes</label>
        <textarea name="decision_notes" class="form-control form-control-sm" rows="3"
            placeholder="Optional notes for the student...">{{ $application->decision_notes }}</textarea>
    </div>

    <button type="submit" class="btn btn-sm text-white w-100" style="background:var(--sm-navy);">
        <i class="bi bi-check-lg me-1"></i>Save Decision
    </button>
</form>
