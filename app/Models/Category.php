<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name','slug','description','is_active'];

    protected $casts = ['is_active'=>'boolean'];

    public function products()
    {
        return $this->hasMany(Product::class);
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
