<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockExtraController;
use App\Http\Controllers\HeldSaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\InvoiceController;

// Root must be login — redirect to /login (or /dashboard if already authed)
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
})->name('home');

// Keep legacy public names so copied blades don't break — redirect to home/POS
Route::get('/admission-calendar', fn() => redirect('/'))->name('public.calendar');
Route::get('/programmes', fn() => view('placeholder', ['title' => 'Products', 'subtitle' => 'Inventory — placeholder (theme preserved)']))->name('public.programmes');
Route::get('/programmes/{programme}', fn($p) => view('placeholder', ['title' => 'Product Detail']))->name('public.programmes.show');
Route::get('/requirements', fn() => view('placeholder', ['title' => 'Requirements']))->name('public.requirements');
Route::get('/fees', fn() => view('placeholder', ['title' => 'Fees']))->name('public.fees');
Route::get('/guidelines', fn() => view('placeholder', ['title' => 'Guidelines']))->name('public.guidelines');
Route::get('/contact', fn() => view('placeholder', ['title' => 'Contact', 'subtitle' => \App\Models\Setting::getValue('shop_phone','+255 700 000 000') . ' · ' . \App\Models\Setting::getValue('shop_email','info@shop.co.tz')]))->name('public.contact');
Route::get('/verify-admission', fn() => view('placeholder', ['title' => 'Verify']))->name('public.verify');

// Auth — POS only (email + password) — pure POS, no admission fields
Route::middleware('guest')->group(function () {
    Route::get('/login', function () { return view('auth.login'); })->name('login');
    Route::post('/login', function (Request $r) {
        $r->validate(['password' => 'required']);
        $loginValue = trim($r->input('login') ?? $r->input('email') ?? '');
        if ($loginValue === '') {
            return back()->withErrors(['email' => 'Email is required.'])->onlyInput('email');
        }
        // POS uses email only; admission index_number / login alias also supported via email column
        $credentials = ['email' => strtolower($loginValue), 'password' => $r->input('password')];
        if (Auth::attempt($credentials, $r->boolean('remember'))) {
            $user = Auth::user();
            if (isset($user->is_active) && ! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Account is deactivated.'])->onlyInput('email');
            }
            $r->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Welcome back, '.($user->name ?? ''));
        }
        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    });
    // Registration disabled — POS accounts are created by admin via /users/create (owner/admin only)
});
Route::match(['get','post'], '/logout', function (Request $r) {
    Auth::logout();
    $r->session()->invalidate();
    $r->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Authenticated areas
Route::middleware(['auth'])->group(function () {
    // Dashboard alias — POS owner dashboard (real stats) — scoped per shop
    Route::get('/dashboard', function () {
        $shopId = currentShopId();
        $today = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at', today());
        $prodBase = \App\Models\Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $todayStats = [
            'transactions' => (clone $today)->count(),
            'products_sold' => \App\Models\SaleItem::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->sum('quantity'),
            'gross_sales' => (clone $today)->sum('subtotal'),
            'discounts' => (clone $today)->sum('discount_amount'),
            'returns' => 0,
            'net_sales' => (clone $today)->sum('total_amount'),
            'cogs' => \App\Models\SaleItem::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->get()->sum(fn($i)=> $i->buying_price * $i->quantity),
            'gross_profit' => (clone $today)->sum('profit_amount'),
            'expenses' => \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('expense_date', today())->sum('amount'),
            'net_profit' => (clone $today)->sum('profit_amount') - \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('expense_date', today())->sum('amount'),
            'cash_collected' => \App\Models\SalePayment::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->where('payment_method','cash')->sum('amount'),
            'mobile_money' => \App\Models\SalePayment::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->whereIn('payment_method',['m-pesa','airtel_money','mixx','halopesa'])->sum('amount'),
        ];
        $inventorySummary = [
            'total_products' => (clone $prodBase)->count(),
            'total_stock' => (clone $prodBase)->sum('current_stock'),
            'stock_value' => (clone $prodBase)->get()->sum(fn($p)=> $p->current_stock * $p->buying_price),
            'low_stock' => (clone $prodBase)->whereColumn('current_stock','<=','min_stock')->where('current_stock','>',0)->count(),
            'out_of_stock' => (clone $prodBase)->where('current_stock','<=',0)->count(),
            'expiring' => (clone $prodBase)->whereNotNull('expiry_date')->whereDate('expiry_date','<=', now()->addDays(30))->count(),
        ];
        $recentSales = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->with('payments')->latest()->limit(5)->get()->map(fn($s)=>['receipt'=>$s->receipt_number,'time'=>$s->created_at->format('H:i'),'total'=>$s->total_amount,'payment'=>$s->payments->first()->payment_method ?? 'cash']);
        $lowStockProducts = (clone $prodBase)->whereColumn('current_stock','<=','min_stock')->limit(5)->get()->map(fn($p)=>['name'=>$p->name,'min'=>$p->min_stock,'stock'=>$p->current_stock]);
        $topProducts = \App\Models\SaleItem::whereHas('product', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('product_id, sum(quantity) as qty, sum(total) as revenue')->groupBy('product_id')->orderByDesc('qty')->limit(3)->with('product')->get()->map(fn($i)=>['name'=>$i->product->name ?? '—','sku'=>$i->product->sku ?? '—','category'=>$i->product->category->name ?? '—','qty'=>$i->qty,'revenue'=>$i->revenue]);
        // Charts data
        $salesTrend = collect(range(6,0))->map(function($i) use ($shopId){
            $d = now()->subDays($i);
            $saleQ = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at',$d->toDateString());
            return ['label'=>$d->format('D'),'date'=>$d->format('m/d'),'total'=>(clone $saleQ)->sum('total_amount'),'count'=>(clone $saleQ)->count()];
        });
        $salesByPayment = \App\Models\SalePayment::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('payment_method, sum(amount) as total, count(*) as cnt')->groupBy('payment_method')->get()->map(fn($r)=>['method'=>$r->payment_method,'total'=>$r->total,'cnt'=>$r->cnt]);
        $salesByCategory = \App\Models\SaleItem::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->join('products','products.id','=','sale_items.product_id')->leftJoin('categories','categories.id','=','products.category_id')->selectRaw('COALESCE(categories.name,\'Uncategorized\') as cat, sum(sale_items.quantity) as qty, sum(sale_items.total) as revenue')->groupBy('cat')->orderByDesc('qty')->limit(5)->get();
        $todayWeekMonth = [
            'today' => \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at', today())->sum('total_amount'),
            'week' => \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereBetween('created_at',[now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount'),
            'month' => \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereBetween('created_at',[now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount'),
        ];
        return view('admin.dashboard', compact('todayStats','inventorySummary','recentSales','lowStockProducts','topProducts','salesTrend','salesByPayment','salesByCategory','todayWeekMonth'));
    })->name('dashboard');

    // Admin prefix — mirrors admision layout but for POS
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', function () {
            $shopId = currentShopId();
            $today = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at', today());
            $prodBase = \App\Models\Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
            $todayStats = [
                'transactions' => (clone $today)->count(),
                'products_sold' => \App\Models\SaleItem::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->sum('quantity'),
                'gross_sales' => (clone $today)->sum('subtotal'),
                'discounts' => (clone $today)->sum('discount_amount'),
                'returns' => 0,
                'net_sales' => (clone $today)->sum('total_amount'),
                'cogs' => \App\Models\SaleItem::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->get()->sum(fn($i)=> $i->buying_price * $i->quantity),
                'gross_profit' => (clone $today)->sum('profit_amount'),
                'expenses' => \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('expense_date', today())->sum('amount'),
                'net_profit' => (clone $today)->sum('profit_amount') - \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('expense_date', today())->sum('amount'),
                'cash_collected' => \App\Models\SalePayment::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->where('payment_method','cash')->sum('amount'),
                'mobile_money' => \App\Models\SalePayment::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->whereIn('payment_method',['m-pesa','airtel_money','mixx','halopesa'])->sum('amount'),
            ];
            $inventorySummary = [
                'total_products' => (clone $prodBase)->count(),
                'total_stock' => (clone $prodBase)->sum('current_stock'),
                'stock_value' => (clone $prodBase)->get()->sum(fn($p)=> $p->current_stock * $p->buying_price),
                'low_stock' => (clone $prodBase)->whereColumn('current_stock','<=','min_stock')->where('current_stock','>',0)->count(),
                'out_of_stock' => (clone $prodBase)->where('current_stock','<=',0)->count(),
                'expiring' => (clone $prodBase)->whereNotNull('expiry_date')->whereDate('expiry_date','<=', now()->addDays(30))->count(),
            ];
            $recentSales = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->with('payments')->latest()->limit(5)->get()->map(fn($s)=>['receipt'=>$s->receipt_number,'time'=>$s->created_at->format('H:i'),'total'=>$s->total_amount,'payment'=>$s->payments->first()->payment_method ?? 'cash']);
            $lowStockProducts = (clone $prodBase)->whereColumn('current_stock','<=','min_stock')->limit(5)->get()->map(fn($p)=>['name'=>$p->name,'min'=>$p->min_stock,'stock'=>$p->current_stock]);
            $topProducts = \App\Models\SaleItem::whereHas('product', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('product_id, sum(quantity) as qty, sum(total) as revenue')->groupBy('product_id')->orderByDesc('qty')->limit(3)->with('product')->get()->map(fn($i)=>['name'=>$i->product->name ?? '—','sku'=>$i->product->sku ?? '—','category'=>$i->product->category->name ?? '—','qty'=>$i->qty,'revenue'=>$i->revenue]);
            $salesTrend = collect(range(6,0))->map(function($i) use ($shopId){ $d = now()->subDays($i); $saleQ = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at',$d->toDateString()); return ['label'=>$d->format('D'),'date'=>$d->format('m/d'),'total'=>(clone $saleQ)->sum('total_amount'),'count'=>(clone $saleQ)->count()]; });
            $salesByPayment = \App\Models\SalePayment::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('payment_method, sum(amount) as total, count(*) as cnt')->groupBy('payment_method')->get()->map(fn($r)=>['method'=>$r->payment_method,'total'=>$r->total,'cnt'=>$r->cnt]);
            $salesByCategory = \App\Models\SaleItem::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->join('products','products.id','=','sale_items.product_id')->leftJoin('categories','categories.id','=','products.category_id')->selectRaw('COALESCE(categories.name,\'Uncategorized\') as cat, sum(sale_items.quantity) as qty, sum(sale_items.total) as revenue')->groupBy('cat')->orderByDesc('qty')->limit(5)->get();
            $todayWeekMonth = ['today'=>\App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at', today())->sum('total_amount'),'week'=>\App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereBetween('created_at',[now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount'),'month'=>\App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereBetween('created_at',[now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount')];
            return view('admin.dashboard', compact('todayStats','inventorySummary','recentSales','lowStockProducts','topProducts','salesTrend','salesByPayment','salesByCategory','todayWeekMonth'));
        })->name('dashboard');
        Route::get('/dashboard', function () {
            $shopId = currentShopId();
            $today = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at', today());
            $prodBase = \App\Models\Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
            $todayStats = [
                'transactions' => (clone $today)->count(),
                'products_sold' => \App\Models\SaleItem::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->sum('quantity'),
                'gross_sales' => (clone $today)->sum('subtotal'),
                'discounts' => (clone $today)->sum('discount_amount'),
                'returns' => 0,
                'net_sales' => (clone $today)->sum('total_amount'),
                'cogs' => \App\Models\SaleItem::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->get()->sum(fn($i)=> $i->buying_price * $i->quantity),
                'gross_profit' => (clone $today)->sum('profit_amount'),
                'expenses' => \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('expense_date', today())->sum('amount'),
                'net_profit' => (clone $today)->sum('profit_amount') - \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('expense_date', today())->sum('amount'),
                'cash_collected' => \App\Models\SalePayment::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->where('payment_method','cash')->sum('amount'),
                'mobile_money' => \App\Models\SalePayment::whereHas('sale', fn($q)=>$q->when($shopId, fn($qq)=>$qq->where('shop_id',$shopId))->whereDate('created_at', today()))->whereIn('payment_method',['m-pesa','airtel_money','mixx','halopesa'])->sum('amount'),
            ];
            $inventorySummary = [
                'total_products' => (clone $prodBase)->count(),
                'total_stock' => (clone $prodBase)->sum('current_stock'),
                'stock_value' => (clone $prodBase)->get()->sum(fn($p)=> $p->current_stock * $p->buying_price),
                'low_stock' => (clone $prodBase)->whereColumn('current_stock','<=','min_stock')->where('current_stock','>',0)->count(),
                'out_of_stock' => (clone $prodBase)->where('current_stock','<=',0)->count(),
                'expiring' => (clone $prodBase)->whereNotNull('expiry_date')->whereDate('expiry_date','<=', now()->addDays(30))->count(),
            ];
            $recentSales = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->with('payments')->latest()->limit(5)->get()->map(fn($s)=>['receipt'=>$s->receipt_number,'time'=>$s->created_at->format('H:i'),'total'=>$s->total_amount,'payment'=>$s->payments->first()->payment_method ?? 'cash']);
            $lowStockProducts = (clone $prodBase)->whereColumn('current_stock','<=','min_stock')->limit(5)->get()->map(fn($p)=>['name'=>$p->name,'min'=>$p->min_stock,'stock'=>$p->current_stock]);
            $topProducts = \App\Models\SaleItem::whereHas('product', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('product_id, sum(quantity) as qty, sum(total) as revenue')->groupBy('product_id')->orderByDesc('qty')->limit(3)->with('product')->get()->map(fn($i)=>['name'=>$i->product->name ?? '—','sku'=>$i->product->sku ?? '—','category'=>$i->product->category->name ?? '—','qty'=>$i->qty,'revenue'=>$i->revenue]);
            $salesTrend = collect(range(6,0))->map(function($i) use ($shopId){ $d = now()->subDays($i); $saleQ = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at',$d->toDateString()); return ['label'=>$d->format('D'),'date'=>$d->format('m/d'),'total'=>(clone $saleQ)->sum('total_amount'),'count'=>(clone $saleQ)->count()]; });
            $salesByPayment = \App\Models\SalePayment::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('payment_method, sum(amount) as total, count(*) as cnt')->groupBy('payment_method')->get()->map(fn($r)=>['method'=>$r->payment_method,'total'=>$r->total,'cnt'=>$r->cnt]);
            $salesByCategory = \App\Models\SaleItem::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->join('products','products.id','=','sale_items.product_id')->leftJoin('categories','categories.id','=','products.category_id')->selectRaw('COALESCE(categories.name,\'Uncategorized\') as cat, sum(sale_items.quantity) as qty, sum(sale_items.total) as revenue')->groupBy('cat')->orderByDesc('qty')->limit(5)->get();
            $todayWeekMonth = ['today'=>\App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereDate('created_at', today())->sum('total_amount'),'week'=>\App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereBetween('created_at',[now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount'),'month'=>\App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->whereBetween('created_at',[now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount')];
            return view('admin.dashboard', compact('todayStats','inventorySummary','recentSales','lowStockProducts','topProducts','salesTrend','salesByPayment','salesByCategory','todayWeekMonth'));
        });
        // Keep legacy admission resource names so old blades still resolve — just show placeholder (excluding POS-managed modules)
        foreach (['academic-years','rounds','windows','workflow','campuses','faculties','departments','programmes','applicants','applications','documents','selection','sms-logs','signatories','support-officers'] as $res) {
            Route::get("/{$res}", fn() => view('placeholder', ['title'=>ucwords(str_replace('-',' ',$res)), 'subtitle'=>'Legacy module — placeholder (theme preserved from admissionsystemnew)']))->name(str_replace('-','.',$res).'.index');
        }
        Route::get('/settings', function(){ return view('settings.index'); })->name('settings.index');
        Route::get('/audit-logs', \App\Http\Controllers\AuditLogController::class)->name('audit-logs.index');
        Route::get('/users', [UserController::class,'index'])->name('users.index');
        Route::get('/users/create', [UserController::class,'create'])->name('users.create');
        Route::post('/users', [UserController::class,'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class,'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class,'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class,'destroy'])->name('users.destroy');
        // Legacy reports
        Route::get('/reports', fn() => view('placeholder', ['title'=>'Reports','subtitle'=>'Legacy admission reports — see POS reports below']))->name('reports.index');
    });

    // POS — core (real CRUD with stock deduction)
    Route::get('/pos', [SaleController::class,'pos'])->name('pos.create');
    Route::post('/pos', [SaleController::class,'storeAjax'])->name('pos.store');
    Route::get('/pos/held', [HeldSaleController::class,'index'])->name('pos.held');
    Route::post('/pos/held', [HeldSaleController::class,'store'])->name('pos.held.store');
    Route::post('/pos/held/{held}/resume', [HeldSaleController::class,'resume'])->name('pos.held.resume');
    Route::delete('/pos/held/{held}', [HeldSaleController::class,'destroy'])->name('pos.held.destroy');
    Route::get('/sales', [SaleController::class,'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class,'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class,'receipt'])->name('sales.receipt');

    Route::get('/sales/{sale}/receipt-page', [SaleController::class,'receiptPage'])->name('sales.receipt-page');

    // Sales Document Workflow — Quotation → Proforma Invoice → Invoice
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class,'index'])->name('index');
        Route::get('/create', [QuotationController::class,'create'])->name('create');
        Route::post('/', [QuotationController::class,'store'])->name('store');
        Route::post('/{id}/status', [QuotationController::class,'markStatus'])->name('status');
        Route::post('/{id}/duplicate', [QuotationController::class,'duplicate'])->name('duplicate');
        Route::post('/{id}/convert-proforma', [QuotationController::class,'convertToProforma'])->name('convert-proforma');
        Route::post('/{id}/convert-invoice', [QuotationController::class,'convertToInvoice'])->name('convert-invoice');
        Route::get('/{id}/print', [QuotationController::class,'print'])->name('print');
Route::get('/{id}/pdf', [QuotationController::class,'pdf'])->name('pdf');
        Route::get('/{id}', [QuotationController::class,'show'])->name('show');
        Route::get('/{id}/edit', [QuotationController::class,'edit'])->name('edit');
        Route::put('/{id}', [QuotationController::class,'update'])->name('update');
        Route::delete('/{id}', [QuotationController::class,'destroy'])->name('destroy');
    });
    Route::prefix('proforma-invoices')->name('proforma-invoices.')->group(function () {
        Route::get('/', [ProformaInvoiceController::class,'index'])->name('index');
        Route::get('/create', [ProformaInvoiceController::class,'create'])->name('create');
        Route::post('/', [ProformaInvoiceController::class,'store'])->name('store');
        Route::post('/{id}/status', [ProformaInvoiceController::class,'markStatus'])->name('status');
        Route::post('/{id}/payments', [ProformaInvoiceController::class,'recordPayment'])->name('payments');
        Route::post('/{id}/convert', [ProformaInvoiceController::class,'convertToInvoice'])->name('convert');
        Route::get('/{id}/print', [ProformaInvoiceController::class,'print'])->name('print');
Route::get('/{id}/pdf', [ProformaInvoiceController::class,'pdf'])->name('pdf');
        Route::get('/{id}', [ProformaInvoiceController::class,'show'])->name('show');
        Route::get('/{id}/edit', [ProformaInvoiceController::class,'edit'])->name('edit');
        Route::put('/{id}', [ProformaInvoiceController::class,'update'])->name('update');
        Route::delete('/{id}', [ProformaInvoiceController::class,'destroy'])->name('destroy');
    });
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class,'index'])->name('index');
        Route::get('/create', [InvoiceController::class,'create'])->name('create');
        Route::post('/', [InvoiceController::class,'store'])->name('store');
        Route::post('/{id}/status', [InvoiceController::class,'markStatus'])->name('status');
        Route::post('/{id}/payments', [InvoiceController::class,'recordPayment'])->name('payments');
        Route::get('/{id}/print', [InvoiceController::class,'print'])->name('print');
Route::get('/{id}/pdf', [InvoiceController::class,'pdf'])->name('pdf');
        Route::get('/{id}', [InvoiceController::class,'show'])->name('show');
        Route::get('/{id}/edit', [InvoiceController::class,'edit'])->name('edit');
        Route::put('/{id}', [InvoiceController::class,'update'])->name('update');
        Route::delete('/{id}', [InvoiceController::class,'destroy'])->name('destroy');
        Route::post('/from-sale/{sale}', [InvoiceController::class,'fromSale'])->name('from-sale');
    });

    // Inventory — Products full CRUD (SKU/barcode unique guard, stock movements, audit)
    Route::get('/products/lookup', [ProductController::class, 'lookup'])->name('products.lookup');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/stock', [StockController::class,'index'])->name('stock.index');
    Route::get('/stock-count', [StockExtraController::class,'count'])->name('stock-count.index');
    Route::get('/adjustments', [StockExtraController::class,'adjustments'])->name('adjustments.index');
    Route::get('/barcodes', [StockExtraController::class,'barcodes'])->name('barcodes.index');
    // Categories / Brands / Units — real live CRUD
    Route::get('/categories', [CategoryController::class,'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class,'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class,'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class,'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class,'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class,'destroy'])->name('categories.destroy');
    Route::get('/brands', [BrandController::class,'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class,'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class,'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandController::class,'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class,'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class,'destroy'])->name('brands.destroy');
    Route::get('/units', [UnitController::class,'index'])->name('units.index');
    Route::get('/units/create', [UnitController::class,'create'])->name('units.create');
    Route::post('/units', [UnitController::class,'store'])->name('units.store');
    Route::get('/units/{unit}/edit', [UnitController::class,'edit'])->name('units.edit');
    Route::put('/units/{unit}', [UnitController::class,'update'])->name('units.update');
    Route::delete('/units/{unit}', [UnitController::class,'destroy'])->name('units.destroy');

    // Purchasing — real with Stock IN on received
    Route::get('/purchases', [PurchaseController::class,'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class,'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class,'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class,'show'])->name('purchases.show');
    Route::delete('/purchases/{purchase}', [PurchaseController::class,'destroy'])->name('purchases.destroy');
    Route::get('/suppliers', [SupplierController::class,'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class,'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class,'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class,'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class,'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class,'destroy'])->name('suppliers.destroy');

    // Customers — real CRUD with encrypted IDs
    Route::get('/customers', [CustomerController::class,'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class,'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class,'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class,'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class,'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class,'destroy'])->name('customers.destroy');
    // Shops — owner multi-shop management
    Route::get('/shops', [\App\Http\Controllers\ShopController::class,'index'])->name('shops.index');
    Route::get('/shops/create', [\App\Http\Controllers\ShopController::class,'create'])->name('shops.create');
    Route::post('/shops', [\App\Http\Controllers\ShopController::class,'store'])->name('shops.store');
    Route::get('/shops/{shop}/edit', [\App\Http\Controllers\ShopController::class,'edit'])->name('shops.edit');
    Route::put('/shops/{shop}', [\App\Http\Controllers\ShopController::class,'update'])->name('shops.update');
    Route::delete('/shops/{shop}', [\App\Http\Controllers\ShopController::class,'destroy'])->name('shops.destroy');
    Route::post('/shops/switch', [\App\Http\Controllers\ShopController::class,'switch'])->name('shops.switch');
    Route::get('/profile', function(){ return view('profile.show'); })->name('profile.show');
    Route::post('/profile/avatar', function(Request $r){
        $r->validate(['avatar'=>'required|image|max:2048']);
        $user = auth()->user();
        $file = $r->file('avatar');
        $dir = public_path('avatars');
        if(!is_dir($dir)) mkdir($dir,0755,true);
        $name = 'avatar_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $file->move($dir, $name);
        $path = 'avatars/'.$name;
        // delete old avatar if exists and not default
        if(!empty($user->avatar) && file_exists(public_path($user->avatar)) && $user->avatar !== $path){
            @unlink(public_path($user->avatar));
        }
        $user->update(['avatar'=>$path]);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_avatar','model_type'=>\App\Models\User::class,'model_id'=>$user->id,'ip_address'=>$r->ip()]);
        return back()->with('success','Profile image updated');
    })->name('profile.avatar');
    Route::get('/account-setting', function(){ return view('profile.account'); })->name('account.setting');
    Route::put('/account-setting', function(Request $r){
        $user = auth()->user();
        $r->validate([
            'name'=>'required|string|max:191',
            'email'=>['required','email',\Illuminate\Validation\Rule::unique('users','email')->ignore($user->id)],
            'phone'=>'nullable|string|max:30',
            'password'=>'nullable|string|min:6|confirmed',
            'current_password'=>'required|string',
        ]);
        if(!\Illuminate\Support\Facades\Hash::check($r->input('current_password'), $user->password)){
            return back()->withErrors(['current_password'=>'Current password is incorrect.'])->withInput();
        }
        $data = ['name'=>$r->input('name'),'email'=>strtolower(trim($r->input('email'))),'phone'=>$r->input('phone')];
        if($r->filled('password')) $data['password'] = $r->input('password');
        $user->update($data);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_account','model_type'=>\App\Models\User::class,'model_id'=>$user->id,'ip_address'=>$r->ip()]);
        return back()->with('success','Account updated');
    })->name('account.update');

    // Returns — live (ReturnController)
    Route::get('/returns', [\App\Http\Controllers\ReturnController::class,'index'])->name('returns.index');
    Route::get('/returns/create', [\App\Http\Controllers\ReturnController::class,'create'])->name('returns.create');
    Route::post('/returns', [\App\Http\Controllers\ReturnController::class,'store'])->name('returns.store');
    Route::get('/returns/{return}', [\App\Http\Controllers\ReturnController::class,'show'])->name('returns.show');
    // Payments — live from sale_payments — scoped per shop
    Route::get('/payments', function(Request $request){
        $q = $request->input('q');
        $shopId = currentShopId();
        $payments = \App\Models\SalePayment::with('sale')
            ->when($shopId, fn($qq)=>$qq->whereHas('sale', fn($s)=>$s->where('shop_id',$shopId)))
            ->when($q, fn($qq)=> $qq->where('reference','like',"%{$q}%")->orWhereHas('sale', fn($s)=>$s->where('receipt_number','like',"%{$q}%")))
            ->latest()->paginate(15)->withQueryString();
        return view('payments.index', compact('payments','q'));
    })->name('payments.index');
    // Expenses — live CRUD
    Route::get('/expenses', [ExpenseController::class,'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class,'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class,'store'])->name('expenses.store');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class,'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class,'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class,'destroy'])->name('expenses.destroy');
    Route::get('/shifts', function(){
        $shopId = currentShopId();
        $shifts = \App\Models\CashierShift::with(['cashier','shop'])->when($shopId, fn($q)=>$q->where('shop_id',$shopId))->latest()->paginate(10);
        return view('shifts.index', compact('shifts'));
    })->name('shifts.index');
    Route::get('/shifts/current', function(){
        $shopId = currentShopId();
        $shift = \App\Models\CashierShift::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->where('cashier_id', auth()->id())->where('status','open')->latest()->first();
        return view('shifts.current', compact('shift'));
    })->name('shifts.current');
    Route::post('/shifts/open', function(\Illuminate\Http\Request $r){
        $r->validate(['opening_cash'=>'required|numeric|min:0']);
        $shift = \App\Models\CashierShift::create(['shop_id'=>currentShopId(),'cashier_id'=>auth()->id(),'opening_cash'=>$r->opening_cash,'status'=>'open','opened_at'=>now()]);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'open_shift','model_type'=>\App\Models\CashierShift::class,'model_id'=>$shift->id,'ip_address'=>$r->ip()]);
        return back()->with('success','Shift opened');
    })->name('shifts.open');
    Route::post('/shifts/{shift}/close', function(\App\Models\CashierShift $shift, \Illuminate\Http\Request $r){
        $shift->update(['closing_cash'=>$r->closing_cash,'actual_cash'=>$r->actual_cash,'status'=>'closed','closed_at'=>now()]);
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'close_shift','model_type'=>\App\Models\CashierShift::class,'model_id'=>$shift->id,'ip_address'=>$r->ip()]);
        return back()->with('success','Shift closed');
    })->name('shifts.close');

    // Reports — real data — per shop
    Route::get('/reports', function(){
        $shopId = currentShopId();
        $saleBase = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $prodBase = \App\Models\Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $expBase = \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $totalSales = (clone $saleBase)->count();
        $totalRevenue = (clone $saleBase)->sum('total_amount');
        $totalProfit = (clone $saleBase)->sum('profit_amount');
        $totalCogs = \App\Models\SaleItem::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->get()->sum(fn($i)=> $i->buying_price * $i->quantity);
        $totalExpenses = (clone $expBase)->sum('amount');
        $netProfit = $totalProfit - $totalExpenses;
        $stockValue = (clone $prodBase)->get()->sum(fn($p)=> $p->current_stock * $p->buying_price);
        $lowStock = (clone $prodBase)->whereColumn('current_stock','<=','min_stock')->where('current_stock','>',0)->count();
        $outStock = (clone $prodBase)->where('current_stock','<=',0)->count();
        $topProducts = \App\Models\SaleItem::whereHas('product', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('product_id, sum(quantity) as qty, sum(total) as revenue')->groupBy('product_id')->orderByDesc('qty')->limit(5)->with('product')->get();
        $recentSales = (clone $saleBase)->with(['cashier','payments'])->latest()->limit(5)->get();
        return view('reports.index', compact('totalSales','totalRevenue','totalProfit','totalCogs','totalExpenses','netProfit','stockValue','lowStock','outStock','topProducts','recentSales'));
    })->name('reports.index');
    Route::get('/reports/sales', function(){
        $shopId = currentShopId();
        $saleBase = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $todayRevenue = (clone $saleBase)->whereDate('created_at', today())->sum('total_amount');
        $today = (clone $saleBase)->whereDate('created_at', today())->count();
        $weekRevenue = (clone $saleBase)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
        $week = (clone $saleBase)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthRevenue = (clone $saleBase)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount');
        $month = (clone $saleBase)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $byPayment = \App\Models\SalePayment::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('payment_method, sum(amount) as total, count(*) as cnt')->groupBy('payment_method')->get();
        return view('reports.sales', compact('todayRevenue','today','weekRevenue','week','monthRevenue','month','byPayment'));
    })->name('reports.sales');
    Route::get('/reports/inventory', function(){
        $shopId = currentShopId();
        $prodBase = \App\Models\Product::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $totalProducts = (clone $prodBase)->count();
        $stockValue = (clone $prodBase)->get()->sum(fn($p)=> $p->current_stock * $p->buying_price);
        $lowStock = (clone $prodBase)->whereColumn('current_stock','<=','min_stock')->where('current_stock','>',0)->count();
        $outStock = (clone $prodBase)->where('current_stock','<=',0)->count();
        $expiring = (clone $prodBase)->whereNotNull('expiry_date')->whereDate('expiry_date','<=', now()->addDays(30))->count();
        $products = (clone $prodBase)->latest()->limit(20)->get();
        return view('reports.inventory', compact('totalProducts','stockValue','lowStock','outStock','expiring','products'));
    })->name('reports.inventory');
    Route::get('/reports/purchases', function(){
        $shopId = currentShopId();
        $purBase = \App\Models\Purchase::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $supBase = \App\Models\Supplier::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $totalPurchases = (clone $purBase)->count();
        $totalPurchaseAmount = (clone $purBase)->sum('total_amount');
        $supplierCount = (clone $supBase)->count();
        $bySupplier = (clone $purBase)->selectRaw('supplier_id, count(*) as cnt, sum(total_amount) as total, sum(paid_amount) as paid')->groupBy('supplier_id')->with('supplier')->get();
        return view('reports.purchases', compact('totalPurchases','totalPurchaseAmount','supplierCount','bySupplier'));
    })->name('reports.purchases');
    Route::get('/reports/financial', function(){
        $shopId = currentShopId();
        $saleBase = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $revenue = (clone $saleBase)->sum('total_amount');
        $cogs = \App\Models\SaleItem::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->get()->sum(fn($i)=> $i->buying_price * $i->quantity);
        $totalProfit = (clone $saleBase)->sum('profit_amount');
        $totalExpenses = \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->sum('amount');
        $netProfit = $totalProfit - $totalExpenses;
        $byPayment = \App\Models\SalePayment::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('payment_method, sum(amount) as total, count(*) as cnt')->groupBy('payment_method')->get();
        return view('reports.financial', compact('revenue','cogs','totalProfit','totalExpenses','netProfit','byPayment'));
    })->name('reports.financial');
    Route::get('/reports/profit', function(){
        $shopId = currentShopId();
        $saleBase = \App\Models\Sale::when($shopId, fn($q)=>$q->where('shop_id',$shopId));
        $salesCount = (clone $saleBase)->count();
        $totalProfit = (clone $saleBase)->sum('profit_amount');
        $totalExpenses = \App\Models\Expense::when($shopId, fn($q)=>$q->where('shop_id',$shopId))->sum('amount');
        $netProfit = $totalProfit - $totalExpenses;
        $byProduct = \App\Models\SaleItem::whereHas('sale', fn($q)=> $shopId ? $q->where('shop_id',$shopId) : $q->whereRaw('1=1'))->selectRaw('product_id, sum(quantity) as qty, sum(total) as revenue, sum(buying_price*quantity) as cogs, sum(profit) as profit')->groupBy('product_id')->orderByDesc('qty')->limit(10)->with('product')->get();
        return view('reports.profit', compact('salesCount','totalProfit','totalExpenses','netProfit','byProduct'));
    })->name('reports.profit');

    // Users — Owner / Admin / Cashier with delete confirmation
    Route::get('/users', [UserController::class,'index'])->name('users.index');
    Route::get('/users/create', [UserController::class,'create'])->name('users.create');
    Route::post('/users', [UserController::class,'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class,'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class,'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class,'destroy'])->name('users.destroy');
    Route::get('/settings', function(){ return view('settings.index'); })->name('settings.index');
    Route::post('/settings', function(Request $r){
        foreach(['shop_name','shop_acronym','shop_phone','shop_email','shop_address','receipt_footer','currency','default_tax'] as $k){
            if($r->has($k)) \App\Models\Setting::setValue($k, $r->input($k));
        }
        \App\Models\Setting::setValue('allow_negative_stock', $r->boolean('allow_negative_stock') ? '1' : '0');
        \App\Models\Setting::setValue('require_customer', $r->boolean('require_customer') ? '1' : '0');
        \App\Models\AuditLog::create(['user_id'=>auth()->id(),'action'=>'update_settings','model_type'=>\App\Models\Setting::class,'ip_address'=>$r->ip()]);
        return back()->with('success','Settings saved');
    })->name('settings.update');
    Route::get('/audit-logs', \App\Http\Controllers\AuditLogController::class)->name('audit-logs.index');

    // Applicant compat alias (cashier)
    Route::get('/applicant/dashboard', fn() => redirect('/pos'))->name('applicant.dashboard');
});

// Fallback legacy applicant routes to POS when not authed (so layout links don't 404)
Route::get('/applicant/history', fn() => view('placeholder', ['title'=>'History']))->name('applicant.history');
Route::get('/applicant/profile', fn() => view('placeholder', ['title'=>'Profile']))->name('applicant.profile');
Route::get('/applicant/calendar', fn() => view('placeholder', ['title'=>'Calendar']))->name('applicant.calendar');
Route::get('/applicant/results', fn() => view('placeholder', ['title'=>'Results']))->name('applicant.results');
