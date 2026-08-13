<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    public $timestamps = false;
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = null;
    protected $fillable = ['key', 'value', 'description', 'updated_by'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $s = static::where('key', $key)->first();
            return $s ? $s->value : $default;
        });
    }
    public static function setValue(string $key, string $value, ?int $updatedBy = null): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'updated_by' => $updatedBy]);
        Cache::forget("setting_{$key}");
    }
}
