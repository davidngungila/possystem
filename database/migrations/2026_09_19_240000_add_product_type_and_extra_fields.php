<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type')->default('Physical')->after('unit_id'); // Physical, Variable, Weighted, Service
            $table->boolean('track_stock')->default(true)->after('product_type');
            $table->boolean('track_batch')->default(false)->after('track_stock');
            $table->boolean('track_expiry')->default(false)->after('track_batch');
            $table->string('catalogue_id')->nullable()->after('id');
        });

        Schema::table('units', function (Blueprint $table) {
            // Ensure we have all needed shop units, check later in seeder
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_type','track_stock','track_batch','track_expiry','catalogue_id']);
        });
    }
};
