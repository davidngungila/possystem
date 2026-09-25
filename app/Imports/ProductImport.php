<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class ProductImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row): Model|array|null
    {
        $barcode = $this->normalizeBarcode($row['barcode'] ?? null);

        if ($barcode !== null && Product::where('barcode', $barcode)->exists()) {
            return null;
        }

        $category = Category::firstOrCreate(
            ['name' => trim($row['category'] ?? '')],
            ['slug' => \Illuminate\Support\Str::slug($row['category'] ?? '') . '-' . \Illuminate\Support\Str::random(4), 'is_active' => true]
        );
        $brand = Brand::firstOrCreate(
            ['name' => trim($row['brand'] ?? '')],
            ['slug' => \Illuminate\Support\Str::slug($row['brand'] ?? '') . '-' . \Illuminate\Support\Str::random(4), 'is_active' => true]
        );
        $unit = Unit::firstOrCreate(
            ['name' => trim($row['unit'] ?? '')],
            ['short_name' => trim($row['unit'] ?? '') . '-U', 'is_active' => true]
        );

        $sku = $this->nullIfEmpty($row['sku'] ?? null);

        return new Product([
            'name' => $this->nullIfEmpty($row['name'] ?? null),
            'sku' => $sku,
            'barcode' => $barcode,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'unit_id' => $unit->id,
            'description' => $this->nullIfEmpty($row['description'] ?? null),
            'specifications' => $this->nullIfEmpty($row['specifications'] ?? null),
            'buying_price' => $this->parsePrice($row['cost price'] ?? $row['cost_price'] ?? 0),
            'selling_price' => $this->parsePrice($row['selling price'] ?? $row['selling_price'] ?? 0),
            'current_stock' => (int)($row['quantity'] ?? $row['qty'] ?? 0),
            'min_stock' => (int)($row['reorder level'] ?? $row['reorder_level'] ?? 0),
            'expiry_date' => $this->parseDate($row['expiry date'] ?? $row['expiry_date'] ?? null),
            'batch_number' => $this->nullIfEmpty($row['batch number'] ?? null),
            'status' => $row['status'] ?? 'active',
            'available_online' => isset($row['available online']) ? filter_var($row['available online'], FILTER_VALIDATE_BOOLEAN) : false,
            'scanned' => isset($row['scanned']) ? filter_var($row['scanned'], FILTER_VALIDATE_BOOLEAN) : false,
            'linked' => isset($row['linked']) ? filter_var($row['linked'], FILTER_VALIDATE_BOOLEAN) : false,
            'shop_id' => currentShopId(),
        ]);
    }

    private function normalizeBarcode($value): ?string
    {
        $barcode = trim((string)($value ?? ''));
        $emptyValues = ['', '-', 'n/a', 'na', 'null', 'none', 'no barcode', 'no_barcode', 'n/a.', '-'];
        if ($barcode === '' || in_array(strtolower($barcode), $emptyValues, true)) {
            return null;
        }
        return $barcode;
    }

    private function nullIfEmpty($value): ?string
    {
        return ($value !== null && $value !== '') ? (string)$value : null;
    }

    private function parsePrice($value): float
    {
        return (float) str_replace(['TZS', ' ', ','], '', $value ?? 0);
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') return null;
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function batchSize(): int { return 500; }
    public function chunkSize(): int { return 500; }
}
