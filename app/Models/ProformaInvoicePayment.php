<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoicePayment extends Model
{
    protected $fillable = ['proforma_invoice_id','payment_method','amount','reference','user_id'];
    protected function casts(): array { return ['amount'=>'float']; }

    public function proformaInvoice(){ return $this->belongsTo(ProformaInvoice::class); }
    public function user(){ return $this->belongsTo(User::class); }
}