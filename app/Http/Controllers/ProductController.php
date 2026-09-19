<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $filter = $request->input('filter', 'all');
        $shopId = currentShopId();

        $products = Product::with(['category','brand','unit','supplier'])
            ->when($shopId, fn($qq)=> $qq->where('shop_id', $shopId))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('sku', 'like', "%{$q}%")
                       ->orWhere('barcode', 'like', "%{$q}%");
                });
            })
            ->when($filter === 'low', fn($qq) => $qq->whereColumn('current_stock','<=','min_stock')->where('current_stock','>',0))
            ->when($filter === 'out', fn($qq) => $qq->where('current_stock','<=',0))
            ->when($filter === 'active', fn($qq) => $qq->where('status','active'))
            ->when($filter === 'expiring', fn($qq) => $qq->whereNotNull('expiry_date')->whereDate('expiry_date','<=', now()->addDays(30)))
            ->latest()
            ->paginate(12)->withQueryString();

        return view('products.index', compact('products','q','filter'));
    }

    public function create()
    {
        $shopId = currentShopId();
        $shop = currentShop();
        $catQuery = Category::where('is_active',1);
        if ($shop && !empty($shop->shopTypesArray())) {
            $types = $shop->shopTypesArray();
            $catQuery->where(function($q) use ($types){
                foreach($types as $t){ $q->orWhereJsonContains('shop_types', $t); }
                $q->orWhereNull('shop_types');
            });
        }
        return view('products.create', [
            'categories' => $catQuery->orderBy('name')->get(),
            'brands' => Brand::where('is_active',1)->orderBy('name')->get(),
            'units' => Unit::where('is_active',1)->orderBy('name')->get(),
            'suppliers' => Supplier::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('is_active',1)->orderBy('name')->get(),
            'currentShop' => $shop,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => ['required','string','max:50', \Illuminate\Validation\Rule::unique('products','sku')->where(fn($q)=>$q->where('shop_id', currentShopId()))],
            'barcode' => ['nullable','string','max:50', \Illuminate\Validation\Rule::unique('products','barcode')->where(fn($q)=>$q->where('shop_id', currentShopId()))],
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,discontinued',
            'description' => 'nullable|string',
        ]);

        $data['shop_id'] = currentShopId();
        $product = Product::create($data);

        if ($product->current_stock > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $product->current_stock,
                'previous_stock' => 0,
                'new_stock' => $product->current_stock,
                'reference_type' => 'initial',
                'reference_id' => $product->id,
                'user_id' => auth()->id(),
                'reason' => 'Initial stock',
            ]);
        }

        // audit
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_product',
            'model_type' => Product::class,
            'model_id' => $product->id,
            'new_values' => $product->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created: '.$product->name);
    }

    public function show($encId)
    {
        $product = Product::findOrFail(decIdOrRaw($encId));
        if($product->shop_id && currentShopId() && $product->shop_id !== currentShopId()) abort(404);
        $product->load(['category','brand','unit','supplier','stockMovements.user']);
        return view('products.show', compact('product'));
    }

    public function edit($encId)
    {
        $product = Product::findOrFail(decIdOrRaw($encId));
        if($product->shop_id && currentShopId() && $product->shop_id !== currentShopId()) abort(404);
        $shopId = currentShopId();
        $shop = currentShop();
        $catQuery = Category::where('is_active',1);
        if ($shop && !empty($shop->shopTypesArray())) {
            $types = $shop->shopTypesArray();
            $catQuery->where(function($q) use ($types){
                foreach($types as $t){ $q->orWhereJsonContains('shop_types', $t); }
                $q->orWhereNull('shop_types');
            });
        }
        return view('products.edit', [
            'product' => $product,
            'categories' => $catQuery->orderBy('name')->get(),
            'brands' => Brand::where('is_active',1)->orderBy('name')->get(),
            'units' => Unit::where('is_active',1)->orderBy('name')->get(),
            'suppliers' => Supplier::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('is_active',1)->orderBy('name')->get(),
            'currentShop' => $shop,
        ]);
    }

    public function update(Request $request, $encId)
    {
        $product = Product::findOrFail(decIdOrRaw($encId));
        if($product->shop_id && currentShopId() && $product->shop_id !== currentShopId()) abort(404);
        $old = $product->toArray();
        $prevStock = $product->current_stock;

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => ['required','string','max:50', Rule::unique('products','sku')->ignore($product->id)->where(fn($q)=>$q->where('shop_id', $product->shop_id))],
            'barcode' => ['nullable','string','max:50', Rule::unique('products','barcode')->ignore($product->id)->where(fn($q)=>$q->where('shop_id', $product->shop_id))],
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,discontinued',
            'description' => 'nullable|string',
        ]);

        $product->update($data);

        // stock movement if stock changed
        if ((int)$data['current_stock'] !== (int)$prevStock) {
            $diff = (int)$data['current_stock'] - (int)$prevStock;
            StockMovement::create([
                'product_id' => $product->id,
                'type' => $diff > 0 ? 'in' : 'adjustment',
                'quantity' => abs($diff),
                'previous_stock' => $prevStock,
                'new_stock' => $data['current_stock'],
                'reference_type' => 'adjustment',
                'reference_id' => $product->id,
                'user_id' => auth()->id(),
                'reason' => $diff > 0 ? 'Stock increase via edit' : 'Stock adjustment via edit',
            ]);
        }

        // detect price change for audit
        if (($old['buying_price'] != $product->buying_price) || ($old['selling_price'] != $product->selling_price)) {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'price_change',
                'model_type' => Product::class,
                'model_id' => $product->id,
                'old_values' => ['buying_price'=>$old['buying_price'],'selling_price'=>$old['selling_price']],
                'new_values' => ['buying_price'=>$product->buying_price,'selling_price'=>$product->selling_price],
                'ip_address' => $request->ip(),
            ]);
        }

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_product',
            'model_type' => Product::class,
            'model_id' => $product->id,
            'old_values' => $old,
            'new_values' => $product->fresh()->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated');
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $product = Product::findOrFail(decIdOrRaw($encId));
        if($product->shop_id && currentShopId() && $product->shop_id !== currentShopId()) abort(404);
        // prevent delete if has sales
        if ($product->saleItems()->exists()) {
            return back()->with('error', 'Cannot delete product with sales history. Set to inactive/discontinued instead.');
        }
        $old = $product->toArray();
        $product->delete();
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_product',
            'model_type' => Product::class,
            'model_id' => $old['id'],
            'old_values' => $old,
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('products.index')->with('success', 'Product deleted');
    }

    // AJAX barcode lookup for POS + catalogue search for product create
    public function lookup(Request $request)
    {
        $q = $request->input('q');
        if (!$q) return response()->json([]);
        $shopId = currentShopId();
        // Include global (null shop_id) sample catalogue plus current shop products
        $base = Product::when($shopId, fn($qq)=>$qq->where(function($qq2) use ($shopId){ $qq2->where('shop_id',$shopId)->orWhereNull('shop_id'); }));
        $p = (clone $base)->where(function($qq) use($q){ $qq->where('barcode',$q)->orWhere('sku',$q); })->first();
        if ($p) return response()->json($p);
        // fallback search by name/sku/barcode
        $list = (clone $base)->where(function($qq) use($q){ $qq->where('name','like',"%{$q}%")->orWhere('sku','like',"%{$q}%")->orWhere('barcode','like',"%{$q}%"); })->limit(8)->get(['id','name','sku','barcode','category_id','brand_id','unit_id','supplier_id','buying_price','selling_price','wholesale_price','tax_rate','description','product_type','current_stock']);
        return response()->json($list);
    }
}

