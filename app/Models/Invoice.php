<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'shop_id','invoice_number','sale_id','quotation_id','proforma_invoice_id','customer_id',
        'invoice_date','due_date',
        'subtotal','discount_type','discount_value','discount_amount',
        'tax_rate','tax_amount','total_amount','paid_amount','balance_due',
        'payment_status','payment_method','notes','terms','salesperson_id','created_by',
    ];
    protected function casts(): array { return ['invoice_date'=>'date','due_date'=>'date','created_at'=>'datetime','updated_at'=>'datetime']; }

    public function shop(){ return $this->belongsTo(Shop::class); }
    public function customer(){ return $this->belongsTo(Customer::class); }
    public function items(){ return $this->hasMany(InvoiceItem::class); }
    public function payments(){ return $this->hasMany(InvoicePayment::class); }
    public function sale(){ return $this->belongsTo(Sale::class); }
    public function quotation(){ return $this->belongsTo(Quotation::class); }
    public function proformaInvoice(){ return $this->belongsTo(ProformaInvoice::class); }
    public function salesperson(){ return $this->belongsTo(User::class,'salesperson_id'); }
    public function isCancelled(){ return $this->payment_status === 'cancelled'; }
}