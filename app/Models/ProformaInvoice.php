<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'shop_id','proforma_number','quotation_id','customer_id','date','valid_until','due_date',
        'payment_terms','bank_details',
        'subtotal','discount_type','discount_value','discount_amount',
        'tax_rate','tax_amount','total_amount','paid_amount','balance_due',
        'status','notes','terms','salesperson_id','converted_invoice_id','created_by',
    ];
    protected function casts(): array { return ['date'=>'date','valid_until'=>'date','due_date'=>'date','created_at'=>'datetime','updated_at'=>'datetime']; }

    public function shop(){ return $this->belongsTo(Shop::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function items(){ return $this->hasMany(ProformaInvoiceItem::class); }
    public function payments(){ return $this->hasMany(ProformaInvoicePayment::class); }
    public function quotation(){ return $this->belongsTo(Quotation::class); }
    public function salesperson(){ return $this->belongsTo(User::class,'salesperson_id'); }
    public function convertedInvoice(){ return $this->belongsTo(Invoice::class,'converted_invoice_id'); }
    public function isConverted(){ return $this->status === 'converted'; }
}