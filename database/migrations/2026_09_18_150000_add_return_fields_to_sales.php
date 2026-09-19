<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales','return_reason')) {
                $table->string('return_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('sales','returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('return_reason');
            }
            if (!Schema::hasColumn('sales','returned_by')) {
                $table->foreignId('returned_by')->nullable()->after('returned_at')->constrained('users')->nullOnDelete();
            }
        });
    }
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales','return_reason')) $table->dropColumn('return_reason');
            if (Schema::hasColumn('sales','returned_at')) $table->dropColumn('returned_at');
            if (Schema::hasColumn('sales','returned_by')) {
                $table->dropForeign(['returned_by']);
                $table->dropColumn('returned_by');
            }
        });
    }
};
