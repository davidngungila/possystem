<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['shop_id','category','description','amount','expense_date','created_by'];
    protected $casts = ['expense_date'=>'date','amount'=>'decimal:2'];
    public function shop(){ return $this->belongsTo(Shop::class); }
    public function creator(){ return $this->belongsTo(User::class,'created_by'); }
}
