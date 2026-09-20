<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'shop_id','name','sku','barcode','category_id','brand_id','unit_id','supplier_id',
        'description','buying_price','selling_price','wholesale_price',
        'current_stock','min_stock','tax_rate','expiry_date','batch_number','image','status',
        'product_type','track_stock','track_batch','track_expiry','catalogue_id','is_sample'
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'expiry_date' => 'date',
        'is_sample' => 'boolean',
    ];

    public function shop(){ return $this->belongsTo(Shop::class); }
    public function category(){ return $this->belongsTo(Category::class); }
    public function brand(){ return $this->belongsTo(Brand::class); }
    public function unit(){ return $this->belongsTo(Unit::class); }
    public function supplier(){ return $this->belongsTo(Supplier::class); }
    public function stockMovements(){ return $this->hasMany(StockMovement::class); }
    public function purchaseItems(){ return $this->hasMany(PurchaseItem::class); }
    public function saleItems(){ return $this->hasMany(SaleItem::class); }

    public function isLowStock(): bool
    {
        return $this->current_stock > 0 && $this->current_stock <= $this->min_stock;
    }
    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }
    public function scopeActive($q){ return $q->where('status','active'); }
}
