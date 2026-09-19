<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Supplier;

$defaults = [
  "categories"=>["Groceries","Beverages","Fresh Food","Personal Care","Baby Care","Household","Electronics","Stationery","Cooking"],
  "brands"=>["Coca-Cola","Azam","Kilimanjaro","Unilever","Nivea","Samsung"],
  "units"=>[["name"=>"Piece","short_name"=>"pc"],["name"=>"Bottle","short_name"=>"bt"],["name"=>"Packet","short_name"=>"pkt"],["name"=>"Box","short_name"=>"box"],["name"=>"Carton","short_name"=>"ctn"],["name"=>"Kilogram","short_name"=>"kg"],["name"=>"Gram","short_name"=>"g"],["name"=>"Liter","short_name"=>"l"],["name"=>"Dozen","short_name"=>"dz"]],
  "suppliers"=>[["name"=>"ABC Wholesalers","phone"=>"+255700000001"],["name"=>"Dar Distributors","phone"=>"+255700000002"]],
];
foreach($defaults["categories"] as $n){
  Category::firstOrCreate(["slug"=>\Illuminate\Support\Str::slug($n)], ["name"=>$n]);
}
foreach($defaults["brands"] as $n){
  Brand::firstOrCreate(["slug"=>\Illuminate\Support\Str::slug($n)], ["name"=>$n]);
}
foreach($defaults["units"] as $u){
  Unit::firstOrCreate(["name"=>$u["name"]], $u);
}
foreach($defaults["suppliers"] as $s){
  Supplier::firstOrCreate(["name"=>$s["name"]], $s);
}
echo "seeded ".Category::count()." categories, ".Brand::count()." brands, ".Unit::count()." units, ".Supplier::count()." suppliers\n";
