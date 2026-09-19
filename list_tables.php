<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$tables = Illuminate\Support\Facades\DB::select("SHOW TABLES");
foreach($tables as $t){ $arr=(array)$t; echo array_values($arr)[0]."\n"; }
