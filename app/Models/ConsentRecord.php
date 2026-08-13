<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentRecord extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'consent_version', 'consented', 'ip_address', 'user_agent'];
    protected function casts(): array
    {
        return ['consented' => 'boolean', 'signed_at' => 'datetime'];
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
