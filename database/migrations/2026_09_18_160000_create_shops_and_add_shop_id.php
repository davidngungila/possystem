<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // shops table
        if (!Schema::hasTable('shops')) {
            Schema::create('shops', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique()->nullable();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ensure at least one shop exists
        $shopId = DB::table('shops')->value('id');
        if (!$shopId) {
            $phone = '+255 700 000 000';
            $email = 'info@shop.co.tz';
            try { if (class_exists(\App\Models\Setting::class)) { $phone = \App\Models\Setting::getValue('shop_phone', $phone) ?? $phone; $email = \App\Models\Setting::getValue('shop_email', $email) ?? $email; } } catch(\Throwable $e){}
            $shopId = DB::table('shops')->insertGetId([
                'name' => 'Main Shop',
                'code' => 'MAIN-001',
                'address' => 'Dar es Salaam',
                'phone' => $phone,
                'email' => $email,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $tables = ['users','products','suppliers','customers','purchases','sales','expenses','cashier_shifts','held_sales'];
        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && !Schema::hasColumn($tbl, 'shop_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
                });
            }
        }

        // backfill existing rows to default shop
        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'shop_id')) {
                DB::table($tbl)->whereNull('shop_id')->update(['shop_id' => $shopId]);
            }
        }

        // categories/brands/units remain global, but products will carry shop_id
    }

    public function down(): void
    {
        $tables = ['held_sales','cashier_shifts','expenses','sales','purchases','customers','suppliers','products','users'];
        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'shop_id')) {
                Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                    // drop foreign first if exists
                    try { $table->dropForeign([$tbl.'_shop_id_foreign']); } catch (\Throwable $e) {}
                    if (Schema::hasColumn($tbl, 'shop_id')) $table->dropColumn('shop_id');
                });
            }
        }
        Schema::dropIfExists('shops');
    }
};
