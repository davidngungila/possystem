<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value', 'group', 'description'])]
class Setting extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public static function getValue(string $key, mixed $default = null): mixed
    {
        try {
            // Gracefully fallback if table does not exist or DB not migrated
            $setting = static::where('key', $key)->first();
            if ($setting && isset($setting->value) && $setting->value !== null && $setting->value !== '') {
                return $setting->value;
            }
        } catch (\Throwable $e) {
            // table missing / connection error -> fallback
        }
        // POS fallbacks mapping legacy university keys to shop keys
        $fallbacks = [
            'university_name' => config('app.name', 'SHOP POS'),
            'university_acronym' => 'SP',
            'shop_name' => config('app.name', 'SHOP POS'),
            'shop_acronym' => 'SP',
        ];
        if (isset($fallbacks[$key]) && $default === null) {
            return $fallbacks[$key];
        }
        return $default;
    }

    public static function setValue(string $key, mixed $value, string $group = 'general'): void
    {
        try {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group],
            );
        } catch (\Throwable $e) {
            // silent fallback if table missing
        }
    }
}
