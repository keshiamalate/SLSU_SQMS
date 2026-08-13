<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\DocumentUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentUploadController extends Controller
{
    public function index(Application $application)
    {
        // Make sure this application belongs to the logged in student
        abort_if($application->user_id !== Auth::id(), 403);

        $application->load('scholarship.requiredDocuments', 'documents');

        $unreadCount = \App\Models\Notification::where(function ($q) {
            $q->where('recipient_id', Auth::id())->orWhere('is_mass', 1);
        })
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', Auth::id()))
            ->count();

        return view('student.documents.index', compact('application', 'unreadCount'));
    }

    public function store(Request $request, Application $application)
    {
        abort_if($application->user_id !== Auth::id(), 403);

        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_name' => 'required|string|max:200',
        ], [
            'document.mimes' => 'Only PDF, JPG, and PNG files are allowed.',
            'document.max' => 'File size must not exceed 5MB.',
        ]);

        $file = $request->file('document');

        // Generate a UUID-based filename so it's never guessable
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents/' . Auth::id(), $storedName, 'private');

        DocumentUpload::create([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedName,
            'storage_path' => $path,
            'file_size_bytes' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'verification_status' => 'pending',
        ]);

        // Update application status to documents_pending if it was matched
        if ($application->status === 'matched') {
            $application->update(['status' => 'applied']);
        }

        AuditLog::record('document.uploaded', null, null, [
            'application_id' => $application->id,
            'filename' => $file->getClientOriginalName(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(DocumentUpload $document)
    {
        abort_if($document->user_id !== Auth::id(), 403);

        // Only allow deletion if not yet verified
        if ($document->verification_status === 'verified') {
            return back()->with('error', 'Verified documents cannot be deleted.');
        }

        Storage::disk('private')->delete($document->storage_path);
        $document->delete();

        AuditLog::record('document.deleted', null, null, [
            'filename' => $document->original_filename,
        ]);

        return back()->with('success', 'Document removed.');
    }

    public function download(DocumentUpload $document): StreamedResponse
    {
        // Students can only download their own documents
        // Admins can download any
        $user = Auth::user();
        if ($user->isStudent() && $document->user_id !== $user->id) {
            abort(403);
        }

        abort_unless(Storage::disk('private')->exists($document->storage_path), 404);

        return Storage::disk('private')->download(
            $document->storage_path,
            $document->original_filename
        );
    }
}
