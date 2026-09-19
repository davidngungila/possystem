<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    protected $fillable = ['shop_id','cashier_id','opening_cash','closing_cash','expected_cash','actual_cash','difference','status','opened_at','closed_at'];
    protected $casts = ['opening_cash'=>'decimal:2','closing_cash'=>'decimal:2','expected_cash'=>'decimal:2','actual_cash'=>'decimal:2','difference'=>'decimal:2','opened_at'=>'datetime','closed_at'=>'datetime'];
    public function shop(){ return $this->belongsTo(Shop::class); }
    public function cashier(){ return $this->belongsTo(User::class,'cashier_id'); }
}