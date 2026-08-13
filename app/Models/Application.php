<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'scholarship_id',
        'academic_score',
        'financial_score',
        'course_score',
        'year_level_score',
        'special_qual_score',
        'weighted_score',
        'ml_probability',
        'final_score',
        'match_label',
        'status',
        'applied_at',
        'reviewed_at',
        'reviewed_by',
        'decision_notes',
    ];
    protected function casts(): array
    {
        return ['applied_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function documents(): HasMany
    {
        return $this->hasMany(DocumentUpload::class);
    }
}
