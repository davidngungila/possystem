<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'shop_id','quotation_number','customer_id','date','valid_until',
        'subtotal','discount_type','discount_value','discount_amount',
        'tax_rate','tax_amount','total_amount','status','notes','terms',
        'salesperson_id','converted_proforma_id','converted_invoice_id','created_by',
    ];
    protected function casts(): array { return ['date'=>'date','valid_until'=>'date','created_at'=>'datetime','updated_at'=>'datetime']; }

    public function shop(){ return $this->belongsTo(Shop::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function items(){ return $this->hasMany(QuotationItem::class); }
    public function salesperson(){ return $this->belongsTo(User::class,'salesperson_id'); }
    public function creator(){ return $this->belongsTo(User::class,'created_by'); }
    public function convertedProforma(){ return $this->belongsTo(ProformaInvoice::class,'converted_proforma_id'); }
    public function convertedInvoice(){ return $this->belongsTo(Invoice::class,'converted_invoice_id'); }
    public function isConverted(){ return $this->status === 'converted'; }
}