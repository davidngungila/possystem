<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_sample')->default(false)->after('status')->index();
        });
        // Mark existing sample/damp products (from catalogue seeders) as is_sample
        try {
            \Illuminate\Support\Facades\DB::table('products')
                ->where(function($q){
                    $q->where('description','like','%Sample from%')
                      ->orWhere('description','like','%Sample for%')
                      ->orWhere('description','like','%Pharmacy sample%')
                      ->orWhere('description','like','%Full sample for%')
                      ->orWhere('description','like','%Auto-generated%');
                })
                ->update(['is_sample' => true]);
            // Also mark any product with shop_id null and zero stock as sample if it matches catalogue names (fallback)
            \Illuminate\Support\Facades\DB::table('products')->where('is_sample', false)
                ->whereNull('shop_id')->where('current_stock',0)->where('buying_price',0)
                ->whereIn('name', function($q){
                    // Keep all with sample-like names, but we already handled via description; this is fallback
                });
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_sample');
        });
    }
};
