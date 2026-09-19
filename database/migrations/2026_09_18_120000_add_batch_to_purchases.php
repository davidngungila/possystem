<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases','batch_number')) {
                $table->string('batch_number')->nullable()->after('invoice_number');
            }
            if (!Schema::hasColumn('purchases','expiry_date')) {
                $table->date('expiry_date')->nullable()->after('batch_number');
            }
        });
    }
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['batch_number','expiry_date']);
        });
    }
};
