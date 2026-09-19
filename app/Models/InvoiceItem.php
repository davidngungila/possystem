<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id','product_id','description','quantity','unit_price','discount','total'];
    protected function casts(): array { return ['quantity'=>'integer']; }

    public function invoice(){ return $this->belongsTo(Invoice::class); }
    public function product(){ return $this->belongsTo(Product::class); }
}