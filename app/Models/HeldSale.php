<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeldSale extends Model
{
    protected $fillable = ['shop_id','cashier_id','reference','cart','total'];
    protected $casts = ['cart'=>'array','total'=>'decimal:2'];
    public function shop(){ return $this->belongsTo(Shop::class); }
    public function cashier(){ return $this->belongsTo(User::class,'cashier_id'); }
}
