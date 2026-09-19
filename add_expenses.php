<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    $expenses = \App\Models\Expense::count();
    echo "expenses: ".$expenses.PHP_EOL;
    if($expenses == 0) {
        \App\Models\Expense::create(['category'=>'Rent','amount'=>500000,'description'=>'Monthly rent','expense_date'=>now()->subDays(10),'created_by'=>1]);
        \App\Models\Expense::create(['category'=>'Electricity','amount'=>120000,'description'=>'Monthly electricity','expense_date'=>now()->subDays(5),'created_by'=>1]);
        \App\Models\Expense::create(['category'=>'Internet','amount'=>50000,'description'=>'Internet bill','expense_date'=>now()->subDays(3),'created_by'=>1]);
        echo 'Added sample expenses'."\n";
    }
} catch(Throwable $e){ echo 'expenses ERR: '.$e->getMessage()."\n"; }
