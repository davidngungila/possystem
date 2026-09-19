<?php
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$enc=encId(1); echo "enc1=".$enc."\n"; echo "dec=".decIdOrRaw($enc)."\n"; echo "raw=".decIdOrRaw("1")."\n";
try{ decId("1"); }catch(Throwable $e){ echo "decId raw fails\n"; }
