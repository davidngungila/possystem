<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    $shifts = \App\Models\CashierShift::with('cashier')->latest()->paginate(10);
    echo "shifts count: ".$shifts->total()."\n";
} catch(Throwable $e){ echo "shifts ERR: ".$e->getMessage()."\n"; }
try {
    $payments = \App\Models\SalePayment::latest()->paginate(10);
    echo "payments count: ".$payments->total()."\n";
} catch(Throwable $e){ echo "payments ERR: ".$e->getMessage()."\n"; }
try {
    $expenses = \App\Models\Expense::latest()->paginate(10);
    echo "expenses count: ".$expenses->total()."\n";
} catch(Throwable $e){ echo "expenses ERR: ".$e->getMessage()."\n"; }
