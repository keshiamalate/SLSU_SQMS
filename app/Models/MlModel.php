<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlModel extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'model_name',
        'version',
        'accuracy',
        'f1_score',
        'precision_score',
        'recall_score',
        'training_records',
        'feature_names',
        'storage_path',
        'is_active',
        'trained_at',
        'deployed_at',
        'created_by',
    ];
    protected function casts(): array
    {
        return ['feature_names' => 'array', 'is_active' => 'boolean', 'trained_at' => 'datetime', 'deployed_at' => 'datetime'];
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
