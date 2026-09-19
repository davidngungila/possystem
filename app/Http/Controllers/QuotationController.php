<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\DocumentCalculations;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    use DocumentCalculations;

    public function index(Request $request)
    {
        $q = $request->input('q');
        $st = $request->input('st');
        $shopId = currentShopId();
        $quotations = Quotation::with(['customer','items'])
            ->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))
            ->when($q, fn($qq)=> $qq->where('quotation_number','like',"%{$q}%")->orWhereHas('customer', fn($c)=>$c->where('name','like',"%{$q}%")) )
            ->when($st && $st !== 'all', fn($qq)=>$qq->where('status',$st))
            ->latest()->paginate(12)->withQueryString();
        return view('quotations.index', compact('quotations','q','st'));
    }

    protected function formData($quotation = null)
    {
        $shopId = currentShopId();
        return [
            'customers' => Customer::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('is_active',1)->orderBy('name')->get(),
            'products' => Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('status','active')->orderBy('name')->get(),
            'salespeople' => User::whereIn('role',['owner','admin'])->orderBy('name')->get(),
            'quotation' => $quotation,
            'number' => $quotation ? $quotation->quotation_number : docNextNumber('QT', Quotation::class),
            'statuses' => ['draft','sent','accepted','rejected','expired','converted'],
        ];
    }

    public function create()
    {
        return view('quotations.form', $this->formData());
    }

    public function store(Request $request)
    {
        return $this->save($request, null);
    }

    public function edit($encId)
    {
        $quotation = Quotation::with('items')->findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        return view('quotations.form', $this->formData($quotation));
    }

    public function update(Request $request, $encId)
    {
        $quotation = Quotation::findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        return $this->save($request, $quotation);
    }

    protected function save(Request $request, $quotation)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'required|date',
            'valid_until' => 'nullable|date',
            'status' => 'required|in:draft,sent,accepted,rejected,expired,converted',
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

        return DB::transaction(function () use ($request, $quotation) {
            [$rows, $subtotal] = $this->docItems($request);
            $tot = $this->docTotals($subtotal, $request->input('discount_type'), (float)$request->input('discount_value',0), (float)$request->input('tax_rate',0));

            $data = [
                'shop_id' => currentShopId(),
                'customer_id' => $request->input('customer_id'),
                'date' => $request->input('date'),
                'valid_until' => $request->input('valid_until'),
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

            if ($quotation) {
                if($quotation->status === 'converted') {
                    return redirect()->route('quotations.show', encId($quotation->id))->with('error','Converted quotation cannot be edited.');
                }
                $quotation->update($data);
                $this->syncDocItems($quotation, QuotationItem::class, $rows);
                AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_quotation','model_type'=>Quotation::class,'model_id'=>$quotation->id,'new_values'=>$quotation->fresh()->toArray(),'ip_address'=>$request->ip()]);
                return redirect()->route('quotations.show', encId($quotation->id))->with('success','Quotation updated: '.$quotation->quotation_number);
            }

            $quot = Quotation::create(array_merge(['quotation_number' => docNextNumber('QT', Quotation::class), 'created_by' => auth()->id()], $data));
            foreach ($rows as $r) { QuotationItem::create(array_merge(['quotation_id'=>$quot->id], $r)); }
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'create_quotation','model_type'=>Quotation::class,'model_id'=>$quot->id,'new_values'=>$quot->toArray(),'ip_address'=>$request->ip()]);
            return redirect()->route('quotations.show', encId($quot->id))->with('success','Quotation created: '.$quot->quotation_number);
        });
    }

    public function show($encId)
    {
        $quotation = Quotation::with(['items.product','customer','salesperson','convertedProforma','convertedInvoice'])->findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        return view('quotations.show', compact('quotation'));
    }

    public function duplicate(Request $request, $encId)
    {
        $quotation = Quotation::with('items')->findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        return DB::transaction(function () use ($request, $quotation) {
            $clone = Quotation::create([
                'shop_id'=>$quotation->shop_id,'quotation_number'=>docNextNumber('QT', Quotation::class),
                'customer_id'=>$quotation->customer_id,'date'=>now()->toDateString(),'valid_until'=>$quotation->valid_until,
                'subtotal'=>$quotation->subtotal,'discount_type'=>$quotation->discount_type,'discount_value'=>$quotation->discount_value,'discount_amount'=>$quotation->discount_amount,
                'tax_rate'=>$quotation->tax_rate,'tax_amount'=>$quotation->tax_amount,'total_amount'=>$quotation->total_amount,
                'status'=>'draft','notes'=>$quotation->notes,'terms'=>$quotation->terms,'salesperson_id'=>$quotation->salesperson_id,'created_by'=>auth()->id(),
            ]);
            foreach ($quotation->items as $it) {
                QuotationItem::create(['quotation_id'=>$clone->id,'product_id'=>$it->product_id,'description'=>$it->description,'quantity'=>$it->quantity,'unit_price'=>$it->unit_price,'discount'=>$it->discount,'total'=>$it->total]);
            }
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'duplicate_quotation','model_type'=>Quotation::class,'model_id'=>$clone->id,'ip_address'=>$request->ip()]);
            return redirect()->route('quotations.edit', encId($clone->id))->with('success','Duplicated as '.$clone->quotation_number);
        });
    }

    public function markStatus(Request $request, $encId)
    {
        $quotation = Quotation::findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        $request->validate(['status'=>'required|in:draft,sent,accepted,rejected,expired,converted']);
        $old = $quotation->status;
        $quotation->update(['status'=>$request->input('status')]);
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'status_quotation','model_type'=>Quotation::class,'model_id'=>$quotation->id,'old_values'=>['status'=>$old],'new_values'=>['status'=>$quotation->status],'ip_address'=>$request->ip()]);
        return back()->with('success','Quotation '.$quotation->quotation_number.' marked '.ucfirst($quotation->status));
    }

    public function destroy(Request $request, $encId)
    {
        if(auth()->check() && auth()->user()->isCashier()) abort(403, 'Cashiers cannot delete records.');
        $quotation = Quotation::findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        if($quotation->status === 'converted'){
            return back()->with('error','Converted quotation cannot be deleted.');
        }
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'delete_quotation','model_type'=>Quotation::class,'model_id'=>$quotation->id,'old_values'=>['number'=>$quotation->quotation_number],'ip_address'=>$request->ip()]);
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success','Quotation deleted');
    }

    public function convertToProforma(Request $request, $encId)
    {
        $quotation = Quotation::with('items')->findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        if($quotation->status === 'converted' && $quotation->converted_proforma_id){
            return redirect()->route('proforma-invoices.show', encId($quotation->converted_proforma_id));
        }
        return DB::transaction(function () use ($request, $quotation) {
            $proforma = ProformaInvoice::create([
                'shop_id'=>$quotation->shop_id,'proforma_number'=>docNextNumber('PI', ProformaInvoice::class),
                'quotation_id'=>$quotation->id,'customer_id'=>$quotation->customer_id,
                'date'=>now()->toDateString(),'valid_until'=>$quotation->valid_until,'due_date'=>now()->addDays(7)->toDateString(),
                'subtotal'=>$quotation->subtotal,'discount_type'=>$quotation->discount_type,'discount_value'=>$quotation->discount_value,'discount_amount'=>$quotation->discount_amount,
                'tax_rate'=>$quotation->tax_rate,'tax_amount'=>$quotation->tax_amount,'total_amount'=>$quotation->total_amount,
                'paid_amount'=>0,'balance_due'=>$quotation->total_amount,
                'status'=>'draft','notes'=>$quotation->notes,'terms'=>$quotation->terms,'salesperson_id'=>$quotation->salesperson_id,'created_by'=>auth()->id(),
            ]);
            foreach ($quotation->items as $it) {
                ProformaInvoiceItem::create(['proforma_invoice_id'=>$proforma->id,'product_id'=>$it->product_id,'description'=>$it->description,'quantity'=>$it->quantity,'unit_price'=>$it->unit_price,'discount'=>$it->discount,'total'=>$it->total]);
            }
            $quotation->update(['status'=>'converted','converted_proforma_id'=>$proforma->id]);
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'convert_quotation_proforma','model_type'=>ProformaInvoice::class,'model_id'=>$proforma->id,'new_values'=>['pi'=>$proforma->proforma_number,'qt'=>$quotation->quotation_number],'ip_address'=>$request->ip()]);
            return redirect()->route('proforma-invoices.show', encId($proforma->id))->with('success','Converted to Proforma '.$proforma->proforma_number);
        });
    }

    public function convertToInvoice(Request $request, $encId)
    {
        $quotation = Quotation::with('items')->findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        if($quotation->status === 'converted' && $quotation->converted_invoice_id){
            return redirect()->route('invoices.show', encId($quotation->converted_invoice_id));
        }
        return DB::transaction(function () use ($request, $quotation) {
            $invoice = Invoice::create([
                'shop_id'=>$quotation->shop_id,'invoice_number'=>docNextNumber('INV', Invoice::class),
                'quotation_id'=>$quotation->id,'proforma_invoice_id'=>null,'customer_id'=>$quotation->customer_id,
                'invoice_date'=>now()->toDateString(),'due_date'=>$quotation->valid_until,
                'subtotal'=>$quotation->subtotal,'discount_type'=>$quotation->discount_type,'discount_value'=>$quotation->discount_value,'discount_amount'=>$quotation->discount_amount,
                'tax_rate'=>$quotation->tax_rate,'tax_amount'=>$quotation->tax_amount,'total_amount'=>$quotation->total_amount,
                'paid_amount'=>0,'balance_due'=>$quotation->total_amount,'payment_status'=>'unpaid',
                'notes'=>$quotation->notes,'terms'=>$quotation->terms,'salesperson_id'=>$quotation->salesperson_id,'created_by'=>auth()->id(),
            ]);
            foreach ($quotation->items as $it) {
                InvoiceItem::create(['invoice_id'=>$invoice->id,'product_id'=>$it->product_id,'description'=>$it->description,'quantity'=>$it->quantity,'unit_price'=>$it->unit_price,'discount'=>$it->discount,'total'=>$it->total]);
            }
            $quotation->update(['status'=>'converted','converted_invoice_id'=>$invoice->id]);
            $proforma = ProformaInvoice::where('quotation_id',$quotation->id)->whereIn('status',['draft','sent','accepted'])->first();
            if ($proforma) {
                $proforma->update(['status'=>'cancelled']);
            }
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'convert_quotation_invoice','model_type'=>Invoice::class,'model_id'=>$invoice->id,'new_values'=>['inv'=>$invoice->invoice_number,'qt'=>$quotation->quotation_number],'ip_address'=>$request->ip()]);
            return redirect()->route('invoices.show', encId($invoice->id))->with('success','Converted to Invoice '.$invoice->invoice_number);
        });
    }

    public function print($encId)
    {
        $quotation = Quotation::with(['items.product','customer','salesperson'])->findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        return view('quotations.print', compact('quotation'));
    }

    public function pdf($encId)
    {
        $quotation = Quotation::with(['items.product','customer','salesperson'])->findOrFail(decIdOrRaw($encId));
        if($quotation->shop_id && currentShopId() && $quotation->shop_id !== currentShopId()) abort(404);
        return Pdf::loadView('quotations.pdf', compact('quotation'))->setPaper('a4')->download('Quotation-'.$quotation->quotation_number.'.pdf');
    }
}