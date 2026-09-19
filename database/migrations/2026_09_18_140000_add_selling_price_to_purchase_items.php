<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items','selling_price')) {
                $table->decimal('selling_price', 12, 2)->nullable()->after('buying_price');
            }
        });
    }
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items','selling_price')) {
                $table->dropColumn('selling_price');
            }
        });
    }
};
