<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['shop_id','name','phone','email','address','tin','contact_person','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function shop(){ return $this->belongsTo(Shop::class); }
    public function products(){ return $this->hasMany(Product::class); }
    public function purchases(){ return $this->hasMany(Purchase::class); }
}
