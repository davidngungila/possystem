<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change shop_type from VARCHAR(255) to TEXT to hold JSON array for multiple types
        // Use raw statement to avoid requiring doctrine/dbal
        try {
            DB::statement('ALTER TABLE shops MODIFY COLUMN shop_type TEXT NULL');
        } catch (\Throwable $e) {
            // Fallback for SQLite or if already TEXT
            if (Schema::hasColumn('shops', 'shop_type')) {
                // For SQLite, just ensure it can hold JSON
            }
        }

        // Convert existing single string values to JSON array for consistency
        $shops = DB::table('shops')->whereNotNull('shop_type')->get(['id','shop_type']);
        foreach ($shops as $shop) {
            $val = $shop->shop_type;
            if ($val === null || $val === '') continue;
            // If already JSON array, skip
            $decoded = json_decode($val, true);
            if (is_array($decoded)) continue;
            // If comma-separated or single, convert to JSON array
            if (str_starts_with($val, '[')) continue;
            $new = json_encode([$val]);
            DB::table('shops')->where('id', $shop->id)->update(['shop_type' => $new]);
        }
    }

    public function down(): void
    {
        // Revert to VARCHAR and convert back to single (first element)
        $shops = DB::table('shops')->get(['id','shop_type']);
        foreach ($shops as $shop) {
            $val = $shop->shop_type;
            $decoded = json_decode($val, true);
            if (is_array($decoded) && count($decoded) > 0) {
                DB::table('shops')->where('id', $shop->id)->update(['shop_type' => $decoded[0]]);
            }
        }
        try {
            DB::statement('ALTER TABLE shops MODIFY COLUMN shop_type VARCHAR(255) NULL');
        } catch (\Throwable $e) {}
    }
};
