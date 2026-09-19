<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\DocumentCalculations;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Product;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use DocumentCalculations;

    public function index(Request $request)
    {
        $q = $request->input('q');
        $st = $request->input('st');
        $shopId = currentShopId();
        $invoices = Invoice::with(['customer','items'])
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('invoice_number','like',"%{$q}%")->orWhereHas('customer', fn($c)=>$c->where('name','like',"%{$q}%")) )
            ->when($st === 'overdue', fn($qq)=>$qq->where('payment_status','unpaid')->whereNotNull('due_date')->whereDate('due_date','<', today()))
            ->when($st && $st !== 'all' && $st !== 'overdue', fn($qq)=>$qq->where('payment_status',$st))
            ->latest()->paginate(12)->withQueryString();
        return view('invoices.index', compact('invoices','q','st'));
    }

    protected function formData($invoice = null)
    {
        $shopId = currentShopId();
        return [
            'customers' => Customer::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('is_active',1)->orderBy('name')->get(),
            'products' => Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('status','active')->orderBy('name')->get(),
            'salespeople' => User::whereIn('role',['owner','admin'])->orderBy('name')->get(),
            'invoice' => $invoice,
            'number' => $invoice ? $invoice->invoice_number : docNextNumber('INV', Invoice::class),
            'paymentStatuses' => ['unpaid','partially_paid','paid','overdue','cancelled'],
        ];
    }

    public function create(Request $request)
    {
        $data = $this->formData();
        $data['source'] = null;
        $data['sourceType'] = null;

        $type = $request->input('from');
        $id = $request->input('id');
        if ($id && in_array($type, ['quotation','proforma','sale'], true)) {
            $source = null;
            if ($type === 'quotation') $source = Quotation::with('items')->find(decIdOrRaw($id));
            if ($type === 'proforma') $source = ProformaInvoice::with(['items','payments'])->find(decIdOrRaw($id));
            if ($type === 'sale') $source = Sale::with(['items.product','payments','customer'])->find(decIdOrRaw($id));
            if ($source && $source->shop_id && currentShopId() && $source->shop_id !== currentShopId()) $source = null;
            $data['source'] = $source;
            $data['sourceType'] = $type;
            if ($source) {
                $data['presets'] = $this->sourcePresets($type, $source);
            }
        }
        return view('invoices.form', $data);
    }

    protected function sourcePresets($type, $source)
    {
        $customerId = $source->customer_id ?? (get_class($source) === Sale::class ? $source->customer_id : null);
        return [
            'customer_id' => $customerId,
            'subtotal' => $source->subtotal,
            'discount_type' => $source->discount_type ?? 'none',
            'discount_value' => $source->discount_value ?? 0,
            'discount_amount' => $source->discount_amount ?? 0,
            'tax_rate' => $source->tax_rate ?? 0,
            'tax_amount' => $source->tax_amount ?? 0,
            'total_amount' => $source->total_amount,
            'paid_amount' => $source->paid_amount ?? 0,
            'payment_method' => method_exists($source,'payments') && $source->payments->first() ? $source->payments->first()->payment_method : null,
            'items' => method_exists($source,'items') ? $source->items : collect(),
        ];
    }

    protected function sourceRows($sourceType, $sourceId)
    {
        $rows = collect();
        if ($sourceType === 'quotation') {
            $s = Quotation::with('items')->find($sourceId);
            if ($s) $rows = $s->items->map(fn($it)=>['product_id'=>$it->product_id,'description'=>$it->description,'quantity'=>$it->quantity,'unit_price'=>$it->unit_price,'discount'=>$it->discount,'total'=>$it->total]);
        } elseif ($sourceType === 'proforma') {
            $s = ProformaInvoice::with('items')->find($sourceId);
            if ($s) $rows = $s->items->map(fn($it)=>['product_id'=>$it->product_id,'description'=>$it->description,'quantity'=>$it->quantity,'unit_price'=>$it->unit_price,'discount'=>$it->discount,'total'=>$it->total]);
        } elseif ($sourceType === 'sale') {
            $s = Sale::with('items')->find($sourceId);
            if ($s) $rows = $s->items->map(fn($it)=>['product_id'=>$it->product_id,'description'=>$it->product->name ?? 'Item','quantity'=>$it->quantity,'unit_price'=>$it->selling_price,'discount'=>$it->discount,'total'=>$it->total]);
        }
        return $rows;
    }

    public function store(Request $request)
    {
        return $this->save($request, null);
    }

    public function edit($encId)
    {
        $invoice = Invoice::with('items')->findOrFail(decIdOrRaw($encId));
        if($invoice->shop_id && currentShopId() && $invoice->shop_id !== currentShopId()) abort(404);
        return view('invoices.form', $this->formData($invoice));
    }

    public function update(Request $request, $encId)
    {
        $invoice = Invoice::findOrFail(decIdOrRaw($encId));
        if($invoice->shop_id && currentShopId() && $invoice->shop_id !== currentShopId()) abort(404);
        return $this->save($request, $invoice);
    }

    protected function save(Request $request, $invoice)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'payment_status' => 'required|in:unpaid,partially_paid,paid,overdue,cancelled',
            'payment_method' => 'nullable|string|max:50',
            'discount_type' => 'required|in:none,fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'salesperson_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:2000',
            'terms' => 'nullable|string|max:2000',
        ]);

        return DB::transaction(function () use ($request, $invoice) {
            $sourceType = $request->input('source_type');
            $sourceId = $sourceType ? decIdOrRaw($request->input('source_id')) : null;

            if ($sourceType && $sourceId) {
                $rows = $this->sourceRows($sourceType, $sourceId);
                $subtotal = $rows->sum('total');
                $saleId = $sourceType === 'sale' ? $sourceId : ($invoice?->sale_id ?? null);
                $pfId = $sourceType === 'proforma' ? $sourceId : ($invoice?->proforma_invoice_id ?? null);
                $qtId = in_array($sourceType, ['quotation','proforma']) ? ($sourceType === 'quotation' ? $sourceId : (ProformaInvoice::find($sourceId)?->quotation_id)) : null;
            } else {
                [$rows, $subtotal] = $this->docItems($request);
                $saleId = $invoice?->sale_id ?? null;
                $pfId = $invoice?->proforma_invoice_id ?? null;
                $qtId = $invoice?->quotation_id ?? null;
                $rows = collect($rows);
            }

            $tot = $this->docTotals($subtotal, $request->input('discount_type'), (float)$request->input('discount_value',0), (float)$request->input('tax_rate',0));

            $data = [
                'shop_id' => currentShopId(),
                'customer_id' => $request->input('customer_id'),
                'invoice_date' => $request->input('invoice_date'),
                'due_date' => $request->input('due_date'),
                'payment_status' => $request->input('payment_status'),
                'payment_method' => $request->input('payment_method'),
                'subtotal' => round($subtotal, 2),
                'discount_type' => $request->input('discount_type'),
                'discount_value' => (float)$request->input('discount_value',0),
                'discount_amount' => $tot['discount_amount'],
                'tax_rate' => (float)$request->input('tax_rate',0),
                'tax_amount' => $tot['tax_amount'],
                'total_amount' => $tot['total_amount'],
                'notes' => $request->input('notes'),
                'terms' => $request->input('terms'),
                'salesperson_id' => $request->input('salesperson_id'),
            ];

            if ($invoice) {
                if($invoice->payment_status === 'cancelled') {
                    return redirect()->route('invoices.show', encId($invoice->id))->with('error','Cancelled invoice cannot be edited.');
                }
                if ($saleId) $data['sale_id'] = $saleId;
                if ($pfId) $data['proforma_invoice_id'] = $pfId;
                if ($qtId) $data['quotation_id'] = $qtId;
                $data['balance_due'] = max(0, $tot['total_amount'] - $invoice->paid_amount);
                $invoice->update($data);
                $this->syncDocItems($invoice, InvoiceItem::class, $rows->toArray());
                AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_invoice','model_type'=>Invoice::class,'model_id'=>$invoice->id,'new_values'=>$invoice->fresh()->toArray(),'ip_address'=>$request->ip()]);
                return redirect()->route('invoices.show', encId($invoice->id))->with('success','Invoice updated: '.$invoice->invoice_number);
            }

            $inv = Invoice::create(array_merge([
                'invoice_number'=>docNextNumber('INV', Invoice::class),
                'sale_id'=>$saleId,'proforma_invoice_id'=>$pfId,'quotation_id'=>$qtId,
                'created_by'=>auth()->id(),'paid_amount'=>0,
            ], $data));
            foreach ($rows as $r) { InvoiceItem::create(array_merge(['invoice_id'=>$inv->id], $r)); }
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'create_invoice','model_type'=>Invoice::class,'model_id'=>$inv->id,'new_values'=>$inv->toArray(),'ip_address'=>$request->ip()]);
            return redirect()->route('invoices.show', encId($inv->id))->with('success','Invoice created: '.$inv->invoice_number);
        });
    }

    public function show($encId)
    {
        $invoice = Invoice::with(['items.product','customer','payments.user','sale','quotation','proformaInvoice','salesperson'])->findOrFail(decIdOrRaw($encId));
        if($invoice->shop_id && currentShopId() && $invoice->shop_id !== currentShopId()) abort(404);
        return view('invoices.show', compact('invoice'));
    }

    public function markStatus(Request $request, $encId)
    {
        $invoice = Invoice::findOrFail(decIdOrRaw($encId));
        if($invoice->shop_id && currentShopId() && $invoice->shop_id !== currentShopId()) abort(404);
        $request->validate(['payment_status'=>'required|in:unpaid,partially_paid,paid,overdue,cancelled']);
        $old = $invoice->payment_status;
        $invoice->update(['payment_status'=>$request->input('payment_status')]);
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'status_invoice','model_type'=>Invoice::class,'model_id'=>$invoice->id,'old_values'=>['payment_status'=>$old],'new_values'=>['payment_status'=>$invoice->payment_status],'ip_address'=>$request->ip()]);
        return back()->with('success','Invoice '.$invoice->invoice_number.' marked '.ucfirst(str_replace('_',' ',$invoice->payment_status)));
    }

    public function recordPayment(Request $request, $encId)
    {
        $invoice = Invoice::findOrFail(decIdOrRaw($encId));
        if($invoice->shop_id && currentShopId() && $invoice->shop_id !== currentShopId()) abort(404);
        $request->validate([
            'payment_method'=>'required|string',
            'amount'=>'required|numeric|min:0.01',
            'reference'=>'nullable|string|max:191',
        ]);
        if($invoice->payment_status === 'cancelled'){
            return back()->with('error','Cannot record payment on a cancelled invoice.');
        }
        return DB::transaction(function () use ($request, $invoice) {
            $amount = (float)$request->input('amount');
            $newPaid = $invoice->paid_amount + $amount;
            $invoice->update([
                'paid_amount'=>$newPaid,
                'balance_due'=>max(0, $invoice->total_amount - $newPaid),
                'payment_method'=>$request->input('payment_method') ?: $invoice->payment_method,
            ]);
            $status = $invoice->payment_status;
            if ($invoice->sale_id) {
                $status = 'paid';
            } else {
                if ($newPaid >= $invoice->total_amount - 0.01) $status = 'paid';
                elseif ($newPaid > 0) $status = 'partially_paid';
                else $status = 'unpaid';
                if (($status === 'unpaid' || $status === 'partially_paid') && $invoice->due_date && $invoice->due_date->lt(today())) $status = 'overdue';
            }
            $invoice->update(['payment_status'=>$status]);
            InvoicePayment::create(['invoice_id'=>$invoice->id,'payment_method'=>$request->input('payment_method'),'amount'=>$amount,'reference'=>$request->input('reference'),'user_id'=>auth()->id()]);
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'pay_invoice','model_type'=>Invoice::class,'model_id'=>$invoice->id,'new_values'=>['amount'=>$amount,'method'=>$request->input('payment_method')],'ip_address'=>$request->ip()]);
            return back()->with('success','Payment TZS '.number_format($amount).' recorded on '.$invoice->invoice_number);
        });
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $invoice = Invoice::findOrFail(decIdOrRaw($encId));
        if($invoice->shop_id && currentShopId() && $invoice->shop_id !== currentShopId()) abort(404);
        if($invoice->sale_id || $invoice->paid_amount > 0 || $invoice->payment_status !== 'unpaid'){
            return back()->with('error','Cannot delete invoice linked to a sale or with payments. Cancel it instead.');
        }
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_invoice','model_type'=>Invoice::class,'model_id'=>$invoice->id,'old_values'=>['number'=>$invoice->invoice_number],'ip_address'=>$request->ip()]);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success','Invoice deleted');
    }

    public function print($encId)
    {
        $invoice = Invoice::with(['items.product','customer','payments','sale','proformaInvoice','quotation','salesperson'])->findOrFail(decIdOrRaw($encId));
        if($invoice->shop_id && currentShopId() && $invoice->shop_id !== currentShopId()) abort(404);
        return view('invoices.print', compact('invoice'));
    }

    public function pdf($encId)
    {
        $invoice = Invoice::with(['items.product','customer','payments','sale','proformaInvoice','quotation','salesperson'])->findOrFail(decIdOrRaw($encId));
        if($invoice->shop_id && currentShopId() && $invoice->shop_id !== currentShopId()) abort(404);
        return Pdf::loadView('invoices.pdf', compact('invoice'))->setPaper('a4')->download('Invoice-'.$invoice->invoice_number.'.pdf');
    }

    public function fromSale(Sale $sale)
    {
        if($sale->shop_id && currentShopId() && $sale->shop_id !== currentShopId()) abort(404);
        $invoice = Invoice::where('sale_id',$sale->id)->first();
        if ($invoice) return redirect()->route('invoices.show', encId($invoice->id));
        return redirect()->route('invoices.create', ['from'=>'sale','id'=>encId($sale->id)]);
    }
}