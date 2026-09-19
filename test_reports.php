<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    $todayRevenue = \App\Models\Sale::whereDate('created_at', today())->sum('total_amount');
    $weekRevenue = \App\Models\Sale::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
    $monthRevenue = \App\Models\Sale::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount');
    $byPayment = \App\Models\SalePayment::selectRaw('payment_method, sum(amount) as total, count(*) as cnt')->groupBy('payment_method')->get();
    echo "todayRevenue: ".number_format($todayRevenue,0)."\n";
    echo "weekRevenue: ".number_format($weekRevenue,0)."\n";
    echo "monthRevenue: ".number_format($monthRevenue,0)."\n";
    echo "byPayment count: ".$byPayment->count()."\n";
} catch(Throwable $e) { echo "ERR: ".$e->getMessage()."\n"; }
