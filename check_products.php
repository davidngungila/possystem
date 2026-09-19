<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$products = \App\Models\Product::count();
echo "Products count: $products\n";
if($products > 0) {
    $products = \App\Models\Product::with(['category','brand','unit'])->take(5)->get();
    foreach($products as $p) {
        echo "ID: {$p->id}, Name: {$p->name}, SKU: {$p->sku}, Stock: {$p->current_stock}\n";
    }
}
