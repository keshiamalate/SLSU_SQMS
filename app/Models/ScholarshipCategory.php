<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarshipCategory extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
    public function scholarships(): HasMany
    {
        return $this->hasMany(Scholarship::class, 'category_id');
    }
}
