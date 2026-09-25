<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row): ?\Illuminate\Database\Eloquent\Model
    {
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

        return new Product([
            'name' => $row['name'] ?? null,
            'sku' => $row['sku'] ?? null,
            'barcode' => $row['barcode'] ?? null,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'unit_id' => $unit->id,
            'description' => $row['description'] ?? null,
            'specifications' => $row['specifications'] ?? null,
            'buying_price' => $row['cost price'] ?? $row['cost_price'] ?? 0,
            'selling_price' => $row['selling price'] ?? $row['selling_price'] ?? 0,
            'current_stock' => $row['quantity'] ?? $row['qty'] ?? 0,
            'min_stock' => $row['reorder level'] ?? $row['reorder_level'] ?? 0,
            'expiry_date' => $row['expiry date'] ?? $row['expiry_date'] ?? null,
            'batch_number' => $row['batch number'] ?? $row['batch_number'] ?? null,
            'status' => $row['status'] ?? 'active',
            'available_online' => isset($row['available online']) ? filter_var($row['available online'], FILTER_VALIDATE_BOOLEAN) : false,
            'scanned' => isset($row['scanned']) ? filter_var($row['scanned'], FILTER_VALIDATE_BOOLEAN) : false,
            'linked' => isset($row['linked']) ? filter_var($row['linked'], FILTER_VALIDATE_BOOLEAN) : false,
            'shop_id' => currentShopId(),
        ]);
    }

    public function batchSize(): int { return 500; }
    public function chunkSize(): int { return 500; }
}
