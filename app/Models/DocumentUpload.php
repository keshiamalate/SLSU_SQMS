<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentUpload extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'application_id',
        'user_id',
        'original_filename',
        'stored_filename',
        'storage_path',
        'file_size_bytes',
        'mime_type',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
