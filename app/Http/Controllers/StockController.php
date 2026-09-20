<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $filter = $request->input('filter', 'all');
        $shopId = currentShopId();

        $base = Product::where('is_sample', false)->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId));
        $products = Product::with(['category','brand','unit'])
            ->where('is_sample', false)
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq) => $qq->where(function($w) use ($q){
                $w->where('name','like',"%{$q}%")->orWhere('sku','like',"%{$q}%")->orWhere('barcode','like',"%{$q}%");
            }))
            ->when($filter==='low', fn($qq)=> $qq->whereColumn('current_stock','<=','min_stock')->where('current_stock','>',0))
            ->when($filter==='out', fn($qq)=> $qq->where('current_stock','<=',0))
            ->when($filter==='expiring', fn($qq)=> $qq->whereNotNull('expiry_date')->whereDate('expiry_date','<=', now()->addDays(30)))
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        $stats = [
            'total_products' => (clone $base)->count(),
            'total_stock' => (clone $base)->sum('current_stock'),
            'stock_value' => (clone $base)->get()->sum(fn($p)=> $p->current_stock * $p->buying_price),
            'low_stock' => (clone $base)->whereColumn('current_stock','<=','min_stock')->where('current_stock','>',0)->count(),
            'out_stock' => (clone $base)->where('current_stock','<=',0)->count(),
            'expiring' => (clone $base)->whereNotNull('expiry_date')->whereDate('expiry_date','<=', now()->addDays(30))->count(),
        ];

        $movements = StockMovement::with(['product','user'])->whereHas('product', fn($qq)=> $shopId ? $qq->where('shop_id',$shopId) : $qq->whereRaw('1=1'))->latest()->limit(10)->get();

        return view('stock.index', compact('products','q','filter','stats','movements'));
    }
}
