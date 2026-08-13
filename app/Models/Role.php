<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model {
    public $timestamps = false;
    protected $fillable = ['name', 'description'];

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
    public function isSuperAdmin(): bool  { return $this->name === 'super_admin'; }
    public function isAdmin(): bool       { return in_array($this->name, ['super_admin','scholarship_admin','verifier']); }
    public function isStudent(): bool     { return $this->name === 'student'; }
}
