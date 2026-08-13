<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuplicateFilterLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'scholarship_id', 'conflict_source', 'filter_result', 'override_by', 'override_reason'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }
    public function overrideBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'override_by');
    }
}
