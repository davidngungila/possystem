<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['shop_id','receipt_number','customer_id','cashier_id','subtotal','discount_amount','tax_amount','total_amount','paid_amount','profit_amount','status','return_reason','returned_at','returned_by'];
    protected function casts(): array { return ['returned_at'=>'datetime','created_at'=>'datetime','updated_at'=>'datetime']; }
    public function shop(){ return $this->belongsTo(Shop::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function cashier(){ return $this->belongsTo(User::class,'cashier_id'); }
    public function returnedBy(){ return $this->belongsTo(User::class,'returned_by'); }
    public function items(){ return $this->hasMany(SaleItem::class); }
    public function payments(){ return $this->hasMany(SalePayment::class); }
}
