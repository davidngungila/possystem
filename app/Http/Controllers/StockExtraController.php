<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockExtraController extends Controller
{
    public function count(Request $request)
    {
        $q = $request->input('q');
        $shopId = currentShopId();
        $products = Product::with(['category'])
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('name','like',"%{$q}%")->orWhere('sku','like',"%{$q}%"))
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        // system vs physical comparison is done via adjustments; we show current stock
        return view('stock.count', compact('products','q'));
    }

    public function adjustments(Request $request)
    {
        $q = $request->input('q');
        $shopId = currentShopId();
        $movements = StockMovement::with(['product','user'])
            ->whereIn('type',['adjustment','damage','expired','lost','return'])
            ->when($shopId, fn($qq)=>$qq->whereHas('product', fn($p)=> $p->where('shop_id',$shopId)))
            ->when($q, fn($qq)=> $qq->whereHas('product', fn($p)=> $p->where('name','like',"%{$q}%")->orWhere('sku','like',"%{$q}%")))
            ->latest()
            ->paginate(15)->withQueryString();

        // also show all movements for audit
        $all = StockMovement::with(['product'])->whereHas('product', fn($p)=> $shopId ? $p->where('shop_id',$shopId) : $p->whereRaw('1=1'))->latest()->limit(5)->get();

        return view('stock.adjustments', compact('movements','all','q'));
    }

    public function barcodes(Request $request)
    {
        $q = $request->input('q');
        $shopId = currentShopId();
        $products = Product::with(['category','brand'])
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('name','like',"%{$q}%")->orWhere('sku','like',"%{$q}%")->orWhere('barcode','like',"%{$q}%"))
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        // detect duplicates per shop
        $dupQuery = Product::when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->select('barcode')->whereNotNull('barcode');
        $duplicates = $dupQuery->groupBy('barcode')->havingRaw('COUNT(*) > 1')->pluck('barcode');

        return view('stock.barcodes', compact('products','q','duplicates'));
    }
}
