<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentExistingScholarship extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'scholarship_name', 'scholarship_type', 'granting_body', 'is_exclusive'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
