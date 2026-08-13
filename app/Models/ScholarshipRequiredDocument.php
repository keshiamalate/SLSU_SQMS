<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipRequiredDocument extends Model
{
    public $timestamps = false;
    protected $fillable = ['scholarship_id', 'document_name', 'description', 'is_mandatory', 'display_order'];
    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }
}
