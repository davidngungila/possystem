<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = ['name','code','address','phone','email','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function users(){ return $this->hasMany(User::class); }
    public function products(){ return $this->hasMany(Product::class); }
    public function purchases(){ return $this->hasMany(Purchase::class); }
    public function sales(){ return $this->hasMany(Sale::class); }
    public function expenses(){ return $this->hasMany(Expense::class); }
    public function suppliers(){ return $this->hasMany(Supplier::class); }
    public function customers(){ return $this->hasMany(Customer::class); }
    public function shifts(){ return $this->hasMany(CashierShift::class, 'shop_id'); }
    public function scopeActive($q){ return $q->where('is_active',1); }
}
