<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\DocumentCalculations;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceItem;
use App\Models\ProformaInvoicePayment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProformaInvoiceController extends Controller
{
    use DocumentCalculations;

    public function index(Request $request)
    {
        $q = $request->input('q');
        $st = $request->input('st');
        $shopId = currentShopId();
        $proformas = ProformaInvoice::with(['customer','items'])
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('proforma_number','like',"%{$q}%")->orWhereHas('customer', fn($c)=>$c->where('name','like',"%{$q}%")) )
            ->when($st && $st !== 'all', fn($qq)=>$qq->where('status',$st))
            ->latest()->paginate(12)->withQueryString();
        return view('proforma-invoices.index', compact('proformas','q','st'));
    }

    protected function formData($proforma = null)
    {
        $shopId = currentShopId();
        return [
            'customers' => Customer::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('is_active',1)->orderBy('name')->get(),
            'products' => Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('status','active')->orderBy('name')->get(),
            'salespeople' => User::whereIn('role',['owner','admin'])->orderBy('name')->get(),
            'proforma' => $proforma,
            'number' => $proforma ? $proforma->proforma_number : docNextNumber('PI', ProformaInvoice::class),
            'statuses' => ['draft','sent','accepted','partially_paid','paid','converted','cancelled','expired'],
            'quotations' => Quotation::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereIn('status',['sent','accepted'])->orderByDesc('id')->limit(50)->get(),
        ];
    }

    public function create(Request $request)
    {
        $data = $this->formData();
        $sourceQt = null;
        if ($request->has('from_quotation')) {
            $sourceQt = Quotation::with('items')->find(decIdOrRaw($request->input('from_quotation')));
            if ($sourceQt && $sourceQt->shop_id && currentShopId() && $sourceQt->shop_id !== currentShopId()) $sourceQt = null;
        }
        $data['sourceQt'] = $sourceQt;
        return view('proforma-invoices.form', $data);
    }

    public function store(Request $request)
    {
        return $this->save($request, null);
    }

    public function edit($encId)
    {
        $proforma = ProformaInvoice::with('items')->findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        return view('proforma-invoices.form', $this->formData($proforma));
    }

    public function update(Request $request, $encId)
    {
        $proforma = ProformaInvoice::findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        return $this->save($request, $proforma);
    }

    protected function save(Request $request, $proforma)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'date' => 'required|date',
            'valid_until' => 'nullable|date',
            'due_date' => 'nullable|date',
            'payment_terms' => 'nullable|string|max:191',
            'bank_details' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,sent,accepted,partially_paid,paid,converted,cancelled,expired',
            'discount_type' => 'required|in:none,fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'salesperson_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:2000',
            'terms' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $proforma) {
            [$rows, $subtotal] = $this->docItems($request);
            $tot = $this->docTotals($subtotal, $request->input('discount_type'), (float)$request->input('discount_value',0), (float)$request->input('tax_rate',0));

            $data = [
                'shop_id' => currentShopId(),
                'quotation_id' => $request->input('quotation_id') ?: ($proforma?->quotation_id ?? null),
                'customer_id' => $request->input('customer_id'),
                'date' => $request->input('date'),
                'valid_until' => $request->input('valid_until'),
                'due_date' => $request->input('due_date'),
                'payment_terms' => $request->input('payment_terms'),
                'bank_details' => $request->input('bank_details'),
                'status' => $request->input('status'),
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

            if ($proforma) {
                if(in_array($proforma->status,['converted','cancelled'])) {
                    return redirect()->route('proforma-invoices.show', encId($proforma->id))->with('error','Converted/cancelled proforma cannot be edited.');
                }
                $data['balance_due'] = max(0, $tot['total_amount'] - $proforma->paid_amount);
                $proforma->update($data);
                $this->syncDocItems($proforma, ProformaInvoiceItem::class, $rows);
                AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_proforma','model_type'=>ProformaInvoice::class,'model_id'=>$proforma->id,'new_values'=>$proforma->fresh()->toArray(),'ip_address'=>$request->ip()]);
                return redirect()->route('proforma-invoices.show', encId($proforma->id))->with('success','Proforma updated: '.$proforma->proforma_number);
            }

            $pf = ProformaInvoice::create(array_merge(['proforma_number' => docNextNumber('PI', ProformaInvoice::class), 'created_by' => auth()->id()], $data, ['paid_amount'=>0,'balance_due'=>$tot['total_amount']]));
            foreach ($rows as $r) { ProformaInvoiceItem::create(array_merge(['proforma_invoice_id'=>$pf->id], $r)); }
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'create_proforma','model_type'=>ProformaInvoice::class,'model_id'=>$pf->id,'new_values'=>$pf->toArray(),'ip_address'=>$request->ip()]);
            return redirect()->route('proforma-invoices.show', encId($pf->id))->with('success','Proforma created: '.$pf->proforma_number);
        });
    }

    public function show($encId)
    {
        $proforma = ProformaInvoice::with(['items.product','customer','payments.user','quotation','convertedInvoice'])->findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        return view('proforma-invoices.show', compact('proforma'));
    }

    public function markStatus(Request $request, $encId)
    {
        $proforma = ProformaInvoice::findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        $request->validate(['status'=>'required|in:draft,sent,accepted,partially_paid,paid,converted,cancelled,expired']);
        $old = $proforma->status;
        $proforma->update(['status'=>$request->input('status')]);
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'status_proforma','model_type'=>ProformaInvoice::class,'model_id'=>$proforma->id,'old_values'=>['status'=>$old],'new_values'=>['status'=>$proforma->status],'ip_address'=>$request->ip()]);
        return back()->with('success','Proforma '.$proforma->proforma_number.' marked '.ucfirst(str_replace('_',' ',$proforma->status)));
    }

    public function recordPayment(Request $request, $encId)
    {
        $proforma = ProformaInvoice::findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        $request->validate([
            'payment_method'=>'required|string',
            'amount'=>'required|numeric|min:0.01',
            'reference'=>'nullable|string|max:191',
        ]);
        if($proforma->status === 'converted' || $proforma->status === 'cancelled'){
            return back()->with('error','Cannot record payment on a converted/cancelled proforma.');
        }
        return DB::transaction(function () use ($request, $proforma) {
            $amount = (float)$request->input('amount');
            $newPaid = $proforma->paid_amount + $amount;
            $proforma->update([
                'paid_amount'=>$newPaid,
                'balance_due'=>max(0, $proforma->total_amount - $newPaid),
            ]);
            $status = $proforma->status;
            if ($newPaid >= $proforma->total_amount - 0.01) $status = 'paid';
            elseif ($newPaid > 0) $status = 'partially_paid';
            else $status = 'draft';
            $proforma->update(['status'=>$status]);
            ProformaInvoicePayment::create(['proforma_invoice_id'=>$proforma->id,'payment_method'=>$request->input('payment_method'),'amount'=>$amount,'reference'=>$request->input('reference'),'user_id'=>auth()->id()]);
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'pay_proforma','model_type'=>ProformaInvoice::class,'model_id'=>$proforma->id,'new_values'=>['amount'=>$amount,'method'=>$request->input('payment_method')],'ip_address'=>$request->ip()]);
            return back()->with('success','Payment TZS '.number_format($amount).' recorded on '.$proforma->proforma_number);
        });
    }

    public function convertToInvoice(Request $request, $encId)
    {
        $proforma = ProformaInvoice::with('items')->findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        if($proforma->status === 'converted' && $proforma->converted_invoice_id){
            return redirect()->route('invoices.show', encId($proforma->converted_invoice_id));
        }
        return DB::transaction(function () use ($request, $proforma) {
            $invoice = Invoice::create([
                'shop_id'=>$proforma->shop_id,'invoice_number'=>docNextNumber('INV', Invoice::class),
                'quotation_id'=>$proforma->quotation_id,'proforma_invoice_id'=>$proforma->id,'customer_id'=>$proforma->customer_id,
                'invoice_date'=>now()->toDateString(),'due_date'=>$proforma->due_date ?? $proforma->valid_until,
                'subtotal'=>$proforma->subtotal,'discount_type'=>$proforma->discount_type,'discount_value'=>$proforma->discount_value,'discount_amount'=>$proforma->discount_amount,
                'tax_rate'=>$proforma->tax_rate,'tax_amount'=>$proforma->tax_amount,'total_amount'=>$proforma->total_amount,
                'paid_amount'=>$proforma->paid_amount,'balance_due'=>max(0,$proforma->total_amount - $proforma->paid_amount),
                'payment_status'=> $proforma->paid_amount >= $proforma->total_amount - 0.01 ? 'paid' : ($proforma->paid_amount > 0 ? 'partially_paid' : 'unpaid'),
                'payment_method'=>$proforma->payments->first()->payment_method ?? null,
                'notes'=>$proforma->notes,'terms'=>$proforma->terms,'salesperson_id'=>$proforma->salesperson_id,'created_by'=>auth()->id(),
            ]);
            foreach ($proforma->items as $it) {
                InvoiceItem::create(['invoice_id'=>$invoice->id,'product_id'=>$it->product_id,'description'=>$it->description,'quantity'=>$it->quantity,'unit_price'=>$it->unit_price,'discount'=>$it->discount,'total'=>$it->total]);
            }
            foreach ($proforma->payments as $p) {
                \App\Models\InvoicePayment::create(['invoice_id'=>$invoice->id,'payment_method'=>$p->payment_method,'amount'=>$p->amount,'reference'=>$p->reference,'user_id'=>$p->user_id]);
            }
            $old = $proforma->status;
            $proforma->update(['status'=>'converted','converted_invoice_id'=>$invoice->id]);
            if ($old === 'sent' || $old === 'accepted') {
                $qt = Quotation::find($proforma->quotation_id);
                if ($qt && $qt->status !== 'converted') { $qt->update(['status'=>'converted','converted_invoice_id'=>$invoice->id]); }
            }
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'convert_proforma_invoice','model_type'=>Invoice::class,'model_id'=>$invoice->id,'new_values'=>['inv'=>$invoice->invoice_number,'pi'=>$proforma->proforma_number],'ip_address'=>$request->ip()]);
            return redirect()->route('invoices.show', encId($invoice->id))->with('success','Converted to Invoice '.$invoice->invoice_number);
        });
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $proforma = ProformaInvoice::findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        if(in_array($proforma->status,['converted','paid']) || $proforma->paid_amount > 0){
            return back()->with('error','Paid/converted proforma cannot be deleted. Cancel it instead.');
        }
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_proforma','model_type'=>ProformaInvoice::class,'model_id'=>$proforma->id,'old_values'=>['number'=>$proforma->proforma_number],'ip_address'=>$request->ip()]);
        $proforma->delete();
        return redirect()->route('proforma-invoices.index')->with('success','Proforma deleted');
    }

    public function print($encId)
    {
        $proforma = ProformaInvoice::with(['items.product','customer','payments','salesperson','quotation'])->findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        return view('proforma-invoices.print', compact('proforma'));
    }

    public function pdf($encId)
    {
        $proforma = ProformaInvoice::with(['items.product','customer','payments','salesperson','quotation'])->findOrFail(decIdOrRaw($encId));
        if($proforma->shop_id && currentShopId() && $proforma->shop_id !== currentShopId()) abort(404);
        return Pdf::loadView('proforma-invoices.pdf', compact('proforma'))->setPaper('a4')->download('Proforma-'.$proforma->proforma_number.'.pdf');
    }
}