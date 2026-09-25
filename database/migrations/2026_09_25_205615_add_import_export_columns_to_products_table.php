<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('specifications')->nullable()->after('description');
            $table->boolean('available_online')->default(false)->after('status');
            $table->boolean('scanned')->default(false)->after('available_online');
            $table->boolean('linked')->default(false)->after('scanned');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['specifications', 'available_online', 'scanned', 'linked']);
        });
    }
};
