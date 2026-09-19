<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name','slug','description','shop_types','is_active'];

    protected $casts = ['is_active'=>'boolean', 'shop_types'=>'array'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function forShopType(?string $shopType): \Illuminate\Database\Eloquent\Builder
    {
        if (!$shopType) return static::query();
        return static::where(function($q) use ($shopType){
            $q->whereJsonContains('shop_types', $shopType)
              ->orWhereNull('shop_types')
              ->orWhere('shop_types','like','%general%');
        });
    }

    public static function forShop(?\App\Models\Shop $shop): \Illuminate\Database\Eloquent\Builder
    {
        if (!$shop || empty($shop->shopTypesArray())) return static::query();
        $types = $shop->shopTypesArray();
        return static::where(function($q) use ($types){
            foreach($types as $t){
                $q->orWhereJsonContains('shop_types', $t);
            }
            $q->orWhereNull('shop_types');
        });
    }

    public function shopTypesArray(): array
    {
        $val = $this->shop_types;
        if (is_array($val)) return $val;
        if (is_string($val) && $val !== '') {
            $decoded = json_decode($val, true);
            if (is_array($decoded)) return $decoded;
            return [$val];
        }
        return [];
    }

    protected static function booted()
    {
        static::creating(function ($c) {
            if (empty($c->slug)) $c->slug = Str::slug($c->name).'-'.Str::random(4);
        });
        static::updating(function ($c) {
            if ($c->isDirty('name') && empty($c->slug)) $c->slug = Str::slug($c->name);
        });
    }
}
