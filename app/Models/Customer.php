<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['shop_id','name','phone','email','address','type','credit_balance','is_active'];
    protected $casts = ['credit_balance'=>'decimal:2','is_active'=>'boolean'];
    public function shop(){ return $this->belongsTo(Shop::class); }
    public function sales(){ return $this->hasMany(Sale::class); }
}
