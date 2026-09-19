<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\Auth;
try {
  $html = view("admin.dashboard", ["todayStats"=>["transactions"=>0,"products_sold"=>0,"gross_sales"=>0,"discounts"=>0,"returns"=>0,"net_sales"=>0,"cogs"=>0,"gross_profit"=>0,"expenses"=>0,"net_profit"=>0,"cash_collected"=>0,"mobile_money"=>0],"inventorySummary"=>["total_products"=>0,"total_stock"=>0,"stock_value"=>0,"low_stock"=>0,"out_of_stock"=>0,"expiring"=>0]])->render();
  echo "dashboard render ok len ".strlen($html)."\n";
} catch(Throwable $e){ echo "dashboard err ".$e->getMessage()."\n".$e->getTraceAsString()."\n"; }
try {
  // simulate login POST handling
  $r = new Illuminate\Http\Request();
  $r->merge(["email"=>"admin@shop.co.tz","password"=>"admin123"]);
  $loginValue = trim($r->input("login") ?? $r->input("email") ?? "");
  echo "loginValue $loginValue\n";
  $ok = Auth::attempt(["email"=>strtolower($loginValue),"password"=>"admin123"]);
  echo "attempt ".($ok?"ok":"fail")."\n";
  if($ok){ Auth::logout(); }
} catch(Throwable $e){ echo "login logic err ".$e->getMessage()."\n"; }
