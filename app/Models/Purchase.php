<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['shop_id','supplier_id','invoice_number','batch_number','expiry_date','status','purchase_date','total_amount','paid_amount','discount_amount','tax_amount','created_by'];
    protected $casts = ['purchase_date'=>'date'];
    public function shop(){ return $this->belongsTo(Shop::class); }
    public function supplier(){ return $this->belongsTo(Supplier::class); }
    public function items(){ return $this->hasMany(PurchaseItem::class); }
    public function creator(){ return $this->belongsTo(User::class,'created_by'); }
}
