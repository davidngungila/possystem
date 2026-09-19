<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $shopId = currentShopId();
        $sales = Sale::with(['customer','cashier'])
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq) => $qq->where('receipt_number','like',"%{$q}%"))
            ->latest()
            ->paginate(15)->withQueryString();
        return view('sales.index', compact('sales','q'));
    }

    public function show($encId)
    {
        $sale = Sale::findOrFail(decIdOrRaw($encId));
        if($sale->shop_id && currentShopId() && $sale->shop_id !== currentShopId()) abort(404);
        $sale->load(['items.product','payments','customer','cashier']);
        return view('sales.show', compact('sale'));
    }

    public function pos()
    {
        $shopId = currentShopId();
        $products = Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('status','active')->where('current_stock','>',0)->orderBy('name')->limit(30)->get();
        $heldCart = session('held_cart');
        $heldRef = session('held_reference');
        // clear after reading
        if($heldCart) session()->forget(['held_cart','held_reference']);
        return view('pos.create', compact('products','heldCart','heldRef'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $shopId = currentShopId();
        return DB::transaction(function () use ($request, $shopId) {
            $subtotal = 0;
            $profit = 0;
            $itemsData = [];
            foreach ($request->input('items') as $it) {
                $product = Product::lockForUpdate()->findOrFail($it['product_id']);
                if($product->shop_id && $product->shop_id !== $shopId) throw new \Exception("Product {$product->name} not in current shop");
                $qty = (int)$it['quantity'];
                $discount = (float)($it['discount'] ?? 0);
                if ($product->current_stock < $qty) {
                    // check negative stock setting
                    $allowNegative = \App\Models\Setting::getValue('allow_negative_stock','0') == '1';
                    if (!$allowNegative) {
                        throw new \Exception("Insufficient stock for {$product->name} (available {$product->current_stock})");
                    }
                }
                $lineTotal = ($product->selling_price * $qty) - $discount;
                $lineProfit = ($product->selling_price - $product->buying_price) * $qty - $discount;
                $subtotal += $lineTotal;
                $profit += $lineProfit;
                $itemsData[] = compact('product','qty','discount','lineTotal','lineProfit');
            }

            $discountAmount = (float)($request->input('discount_amount',0));
            $taxAmount = (float)($request->input('tax_amount',0));
            $total = $subtotal - $discountAmount + $taxAmount;

            $paid = collect($request->input('payments'))->sum('amount');
            if ($paid < $total - 0.01) {
                throw new \Exception("Paid amount TZS ".number_format($paid)." is less than total TZS ".number_format($total));
            }

            $receipt = 'INV-'.str_pad((string)(Sale::max('id')+1), 6, '0', STR_PAD_LEFT).'-'.Str::upper(Str::random(4));

            $sale = Sale::create([
                'shop_id' => $shopId,
                'receipt_number' => $receipt,
                'customer_id' => $request->input('customer_id'),
                'cashier_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'paid_amount' => $paid,
                'profit_amount' => $profit - $discountAmount,
                'status' => 'completed',
            ]);

            foreach ($itemsData as $d) {
                $p = $d['product'];
                $qty = $d['qty'];
                $prev = $p->current_stock;
                $new = $prev - $qty;
                // update stock
                $p->decrement('current_stock', $qty);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $p->id,
                    'quantity' => $qty,
                    'selling_price' => $p->selling_price,
                    'buying_price' => $p->buying_price,
                    'discount' => $d['discount'],
                    'total' => $d['lineTotal'],
                    'profit' => $d['lineProfit'],
                ]);

                StockMovement::create([
                    'product_id' => $p->id,
                    'type' => 'out',
                    'quantity' => $qty,
                    'previous_stock' => $prev,
                    'new_stock' => $new,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'user_id' => auth()->id(),
                    'reason' => 'Sale '.$receipt,
                ]);
            }

            foreach ($request->input('payments') as $pay) {
                if ((float)$pay['amount'] <= 0) continue;
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method' => $pay['method'],
                    'amount' => $pay['amount'],
                    'reference' => $pay['reference'] ?? null,
                ]);
            }

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create_sale',
                'model_type' => Sale::class,
                'model_id' => $sale->id,
                'new_values' => $sale->toArray(),
                'ip_address' => $request->ip(),
            ]);

            return $sale;
        });
    }

public function receipt($encId)
    {
        $sale = Sale::findOrFail(decIdOrRaw($encId));
        if($sale->shop_id && currentShopId() && $sale->shop_id !== currentShopId()) abort(404);
        $sale->load(['items.product','payments','customer','cashier']);
        return view('sales.receipt', compact('sale'));
    }

    public function receiptPage($encId)
    {
        $sale = Sale::findOrFail(decIdOrRaw($encId));
        if($sale->shop_id && currentShopId() && $sale->shop_id !== currentShopId()) abort(404);
        $sale->load(['items.product','payments','customer','cashier']);
        return view('sales.receipt-page', compact('sale'));
    }

    // AJAX endpoint for POS to create sale via JSON
    public function storeAjax(Request $request)
    {
        try {
            $sale = $this->store($request);
            return response()->json(['success'=>true,'receipt'=>$sale->receipt_number,'id'=>encId($sale->id), 'receipt_url'=> route('sales.receipt', encId($sale->id))]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()], 422);
        }
    }
}

