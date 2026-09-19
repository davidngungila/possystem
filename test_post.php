<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create("/login", "POST", ["email"=>"admin@shop.co.tz","password"=>"admin123","_token"=>csrf_token()]);
$request->headers->set("Cookie", "");
// need to handle session and csrf - we can disable csrf for test by calling route without middleware? Simpler to test Auth directly and dashboard render with auth
use Illuminate\Support\Facades\Auth;
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if(Auth::attempt(["email"=>"admin@shop.co.tz","password"=>"admin123"])){
  echo "attempt ok user ".Auth::id()."\n";
  try {
    $html = view("admin.dashboard", ["todayStats"=>["transactions"=>0,"products_sold"=>0,"gross_sales"=>0,"discounts"=>0,"returns"=>0,"net_sales"=>0,"cogs"=>0,"gross_profit"=>0,"expenses"=>0,"net_profit"=>0,"cash_collected"=>0,"mobile_money"=>0],"inventorySummary"=>["total_products"=>0,"total_stock"=>0,"stock_value"=>0,"low_stock"=>0,"out_of_stock"=>0,"expiring"=>0]])->render();
    echo "dashboard render ok ".strlen($html)."\n";
  } catch(Throwable $e){ echo "dashboard err ".$e->getMessage()."\n".$e->getTraceAsString()."\n"; }
  Auth::logout();
} else echo "attempt fail\n";
