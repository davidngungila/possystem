<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoiceItem extends Model
{
    protected $fillable = ['proforma_invoice_id','product_id','description','quantity','unit_price','discount','total'];
    protected function casts(): array { return ['quantity'=>'integer']; }

    public function proformaInvoice(){ return $this->belongsTo(ProformaInvoice::class); }
    public function product(){ return $this->belongsTo(Product::class); }
}