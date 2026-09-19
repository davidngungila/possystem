<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Product;
use Illuminate\Http\Request;

trait DocumentCalculations
{
    protected function docItems(Request $request, array $itemKey = ['product_id','quantity','unit_price','discount'])
    {
        $rows = [];
        $subtotal = 0;
        if (! is_array($request->input('items'))) {
            return [collect(), 0];
        }
        foreach ($request->input('items') as $it) {
            $productId = $it[$itemKey[0]] ?? null;
            $qty = (float)($it[$itemKey[1]] ?? 0);
            $price = (float)($it[$itemKey[2]] ?? 0);
            $disc = (float)($it[$itemKey[3]] ?? 0);
            if ($qty <= 0) continue;

            $description = $it['description'] ?? null;
            if ($productId) {
                $p = Product::find($productId);
                if ($p) {
                    $description = $description ?: ($p->name.($p->sku ? ' ('.$p->sku.')' : ''));
                    $productId = $p->id;
                }
            }
            $lineTotal = ($qty * $price) - $disc;
            $subtotal += $lineTotal;
            $rows[] = [
                'product_id' => $productId ? (int) $productId : null,
                'description' => $description ?: null,
                'quantity' => $qty,
                'unit_price' => $price,
                'discount' => $disc,
                'total' => $lineTotal,
            ];
        }
        return [collect($rows), $subtotal];
    }

    protected function docTotals(float $subtotal, string $discountType, float $discountValue, float $taxRate)
    {
        $discountAmount = 0;
        if ($discountType === 'fixed') {
            $discountAmount = min($discountValue, max(0, $subtotal));
        } elseif ($discountType === 'percent') {
            $discountAmount = $subtotal * $discountValue / 100;
        }
        $taxable = max(0, $subtotal - $discountAmount);
        $taxAmount = $taxable * $taxRate / 100;
        $total = $taxable + $taxAmount;
        return [
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($total, 2),
        ];
    }

    protected function syncDocItems($doc, string $itemModel, array $rows)
    {
        $itemModel::where($this->docFk($doc), $doc->id)->delete();
        foreach ($rows as $r) {
            $itemModel::create(array_merge([$this->docFk($doc) => $doc->id], $r));
        }
    }

    protected function docFk($doc)
    {
        return strtolower(class_basename($doc)).'_id';
    }
}