<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return Product::with(['category','brand','unit'])
            ->when(currentShopId(), fn($q) => $q->where('shop_id', currentShopId()))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'SKU', 'Barcode', 'Scanned', 'Linked', 'Category',
            'Brand', 'Unit', 'Description', 'Specifications', 'Cost Price',
            'Selling Price', 'Quantity', 'Reorder Level', 'Expiry Date',
            'Batch Number', 'Status', 'Available Online', 'Created At',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->barcode,
            $product->scanned ? 'Yes' : 'No',
            $product->linked ? 'Yes' : 'No',
            $product->category->name ?? '',
            $product->brand->name ?? '',
            $product->unit->short_name ?? $product->unit->name ?? '',
            $product->description ?? '',
            $product->specifications ?? '',
            $product->buying_price,
            $product->selling_price,
            $product->current_stock,
            $product->min_stock,
            $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '',
            $product->batch_number ?? '',
            $product->status,
            $product->available_online ? 'Yes' : 'No',
            $product->created_at ? $product->created_at->format('Y-m-d H:i:s') : '',
        ];
    }

    public function title(): string
    {
        return 'Products';
    }
}
