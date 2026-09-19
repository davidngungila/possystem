<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $fillable = ['invoice_id','payment_method','amount','reference','user_id'];
    protected function casts(): array { return ['amount'=>'float']; }

    public function invoice(){ return $this->belongsTo(Invoice::class); }
    public function user(){ return $this->belongsTo(User::class); }
}