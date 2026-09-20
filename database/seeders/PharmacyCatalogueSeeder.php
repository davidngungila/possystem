<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Shop;
use App\Models\Brand;

class PharmacyCatalogueSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure pharmacy shop exists
        $pharmaShop = Shop::whereJsonContains('shop_type', 'pharmacy')->first();
        $defaultShopId = $pharmaShop?->id;

        // Ensure categories for pharmacy
        $catNames = ['Health & Pharmacy','Personal Care','Baby Care'];
        $catIds = [];
        foreach ($catNames as $cn) {
            $cat = Category::firstOrCreate(['slug'=>Str::slug($cn)], ['name'=>$cn, 'description'=>'Pharmacy category', 'is_active'=>true, 'shop_types'=>['pharmacy']]);
            // Ensure shop_types includes pharmacy
            if (empty($cat->shop_types) || !in_array('pharmacy', (array)$cat->shop_types)) {
                $cat->shop_types = array_unique(array_merge($cat->shopTypesArray(), ['pharmacy']));
                $cat->save();
            }
            $catIds[$cn] = $cat->id;
        }

        // Units for pharmacy
        $unitNames = ['Pack','Bottle','Box','Piece','Sachet','Pair','Roll','Tin','Tube'];
        $unitIds = [];
        foreach ($unitNames as $un) {
            $u = Unit::where('name', $un)->first() ?? Unit::where('short_name', strtolower($un))->first();
            if (!$u) $u = Unit::firstOrCreate(['name'=>$un], ['short_name'=>strtolower(substr($un,0,3)), 'is_active'=>true]);
            $unitIds[$un] = $u->id;
        }

        $pharmacyProducts = $this->pharmacyData();

        $created = 0; $skipped = 0;
        foreach ($pharmacyProducts as $row) {
            [$name, $category, $unit, $type] = $row;
            if (Product::where('name', $name)->exists()) { $skipped++; continue; }
            $categoryId = $catIds[$category] ?? $catIds['Health & Pharmacy'] ?? Category::first()->id;
            $unitId = $unitIds[$unit] ?? $unitIds['Pack'] ?? Unit::first()->id;
            $sku = strtoupper(Str::slug(substr($name,0,10), '-')).'-PH'.strtoupper(Str::random(3)).rand(10,99);
            Product::create([
                'shop_id' => $defaultShopId,
                'name' => $name,
                'sku' => $sku,
                'barcode' => null,
                'category_id' => $categoryId,
                'brand_id' => null,
                'unit_id' => $unitId,
                'supplier_id' => null,
                'buying_price' => 0,
                'selling_price' => 0,
                'wholesale_price' => null,
                'current_stock' => 0,
                'min_stock' => 5,
                'tax_rate' => 0,
                'product_type' => $type,
                'track_stock' => true,
                'track_batch' => in_array($category, ['Health & Pharmacy']),
                'track_expiry' => in_array($category, ['Health & Pharmacy','Baby Care']),
                'status' => 'active',
                'is_sample' => true,
                'description' => $name.' — Pharmacy sample',
            ]);
            $created++;
        }

        // Generate additional synthetic pharmacy products to ensure >500 for pharmacy
        $targetPharmacy = 550;
        $currentPharmacy = Product::whereHas('category', function($q){ $q->whereJsonContains('shop_types','pharmacy'); })->count();
        // Also count products with category Health & Pharmacy regardless of shop_types
        $currentPharmacy2 = Product::whereIn('category_id', array_values($catIds))->count();
        $current = max($currentPharmacy, $currentPharmacy2);
        $needed = $targetPharmacy - $current;
        if ($needed > 0) {
            $adjectives = ['Premium','Standard','Deluxe','Classic','Super','Eco','Pro','Lite','Max','Prime','Extra','Gold','Fresh','Natural'];
            $suffixes = ['Plus','Extra','Gold','Silver','Fresh','Natural','Organic','Family','Jumbo','Mini',' Forte',' 20s',' 10s',' 100ml',' 30s',' Bottle',' Pack'];
            $types = ['Physical','Variable'];
            for ($i=0; $i<$needed; $i++) {
                $baseCat = array_rand($catIds);
                $baseUnit = array_rand($unitIds);
                $adj = $adjectives[array_rand($adjectives)];
                $suf = $suffixes[array_rand($suffixes)];
                $name = "Pharmacy $adj $baseCat $suf ".rand(100,999);
                if (Product::where('name', $name)->exists()) continue;
                $sku = 'PH-'.strtoupper(Str::random(5)).rand(10,99);
                Product::create([
                    'shop_id' => $defaultShopId,
                    'name' => $name,
                    'sku' => $sku,
                    'barcode' => null,
                    'category_id' => $catIds[$baseCat] ?? $catIds['Health & Pharmacy'],
                    'brand_id' => null,
                    'unit_id' => $unitIds[$baseUnit] ?? $unitIds['Pack'],
                    'supplier_id' => null,
                    'buying_price' => 0,
                    'selling_price' => 0,
                    'wholesale_price' => null,
                    'current_stock' => 0,
                    'min_stock' => 5,
                    'tax_rate' => 0,
                    'product_type' => $types[array_rand($types)],
                    'track_stock' => true,
                    'track_batch' => true,
                    'track_expiry' => true,
                    'status' => 'active',
                    'is_sample' => true,
                    'description' => $name.' — Auto-generated pharmacy sample',
                ]);
                $created++;
            }
        }

        $totalPharmacy = Product::whereIn('category_id', array_values($catIds))->count();
        $this->command->info("Pharmacy catalogue: $created created, $skipped skipped. Pharmacy category products: $totalPharmacy. Total products: ".Product::count());
    }

    private function pharmacyData(): array
    {
        // [Product Name, Category, Unit, Product Type]
        return [
            ['Paracetamol 500mg Tablets 10s','Health & Pharmacy','Pack','Physical'],
            ['Paracetamol 500mg Tablets 100s','Health & Pharmacy','Box','Physical'],
            ['Paracetamol 120mg/5ml Suspension 60ml','Health & Pharmacy','Bottle','Physical'],
            ['Paracetamol 250mg/5ml Suspension 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Ibuprofen 200mg Tablets 20s','Health & Pharmacy','Pack','Physical'],
            ['Ibuprofen 400mg Tablets 20s','Health & Pharmacy','Pack','Physical'],
            ['Ibuprofen 100mg/5ml Suspension 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Oral Rehydration Salts Sachet','Health & Pharmacy','Sachet','Physical'],
            ['Antacid Tablets 20s','Health & Pharmacy','Pack','Physical'],
            ['Antacid Suspension 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Cetirizine 10mg Tablets 10s','Health & Pharmacy','Pack','Physical'],
            ['Loratadine 10mg Tablets 10s','Health & Pharmacy','Pack','Physical'],
            ['Omeprazole 20mg Capsules 14s','Health & Pharmacy','Pack','Physical'],
            ['Omeprazole 40mg Capsules 14s','Health & Pharmacy','Pack','Physical'],
            ['Multivitamin Tablets 30s','Health & Pharmacy','Box','Physical'],
            ['Vitamin C 500mg Tablets 30s','Health & Pharmacy','Box','Physical'],
            ['Vitamin C 1000mg Effervescent 20s','Health & Pharmacy','Box','Physical'],
            ['Zinc Tablets 20mg 20s','Health & Pharmacy','Pack','Physical'],
            ['Ferrous Sulfate Tablets 200mg 30s','Health & Pharmacy','Box','Physical'],
            ['Folic Acid Tablets 5mg 100s','Health & Pharmacy','Box','Physical'],
            ['Cough Syrup 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Expectorant Syrup 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Throat Lozenges 16s','Health & Pharmacy','Pack','Physical'],
            ['Saline Nasal Drops 10ml','Health & Pharmacy','Bottle','Physical'],
            ['Saline Nasal Spray 30ml','Health & Pharmacy','Bottle','Physical'],
            ['Eye Lubricating Drops 10ml','Health & Pharmacy','Bottle','Physical'],
            ['Antiseptic Solution 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Antiseptic Solution 500ml','Health & Pharmacy','Bottle','Physical'],
            ['Hydrogen Peroxide Solution 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Surgical Spirit 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Isopropyl Alcohol 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Povidone Iodine Solution 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Calamine Lotion 100ml','Health & Pharmacy','Bottle','Physical'],
            ['Petroleum Jelly 100g','Personal Care','Tin','Physical'],
            ['Petroleum Jelly 250g','Personal Care','Tin','Physical'],
            ['Antifungal Cream 15g','Health & Pharmacy','Pack','Physical'],
            ['Antifungal Cream 30g','Health & Pharmacy','Pack','Physical'],
            ['Antibacterial Skin Cream 15g','Health & Pharmacy','Pack','Physical'],
            ['Hydrocortisone Cream 1% 15g','Health & Pharmacy','Pack','Physical'],
            ['Zinc Oxide Cream 20g','Health & Pharmacy','Pack','Physical'],
            ['Disposable Syringe 2ml','Health & Pharmacy','Piece','Physical'],
            ['Disposable Syringe 5ml','Health & Pharmacy','Piece','Physical'],
            ['Disposable Syringe 10ml','Health & Pharmacy','Piece','Physical'],
            ['Disposable Syringe 20ml','Health & Pharmacy','Piece','Physical'],
            ['Insulin Syringe','Health & Pharmacy','Piece','Physical'],
            ['Examination Gloves Small','Health & Pharmacy','Box','Physical'],
            ['Examination Gloves Medium','Health & Pharmacy','Box','Physical'],
            ['Examination Gloves Large','Health & Pharmacy','Box','Physical'],
            ['Surgical Gloves Medium','Health & Pharmacy','Pair','Physical'],
            ['Surgical Mask 3-Ply','Health & Pharmacy','Box','Physical'],
            ['N95 Respirator Medium','Health & Pharmacy','Piece','Physical'],
            ['Cotton Wool 50g','Health & Pharmacy','Pack','Physical'],
            ['Cotton Wool 100g','Health & Pharmacy','Pack','Physical'],
            ['Cotton Wool 500g','Health & Pharmacy','Pack','Physical'],
            ['Gauze Swabs 10cm x 10cm','Health & Pharmacy','Pack','Physical'],
            ['Adhesive Bandage Small','Health & Pharmacy','Box','Physical'],
            ['Adhesive Bandage Medium','Health & Pharmacy','Box','Physical'],
            ['Adhesive Bandage Large','Health & Pharmacy','Box','Physical'],
            ['Elastic Bandage 5cm','Health & Pharmacy','Roll','Physical'],
            ['Elastic Bandage 7.5cm','Health & Pharmacy','Roll','Physical'],
            ['Elastic Bandage 10cm','Health & Pharmacy','Roll','Physical'],
            ['Medical Tape 1 Inch','Health & Pharmacy','Roll','Physical'],
            ['Medical Tape 2 Inch','Health & Pharmacy','Roll','Physical'],
            ['Plaster of Paris Bandage','Health & Pharmacy','Roll','Physical'],
            ['Disposable Face Shield','Health & Pharmacy','Piece','Physical'],
            ['Digital Thermometer','Health & Pharmacy','Piece','Physical'],
            ['Infrared Thermometer','Health & Pharmacy','Piece','Physical'],
            ['Blood Pressure Monitor','Health & Pharmacy','Piece','Physical'],
            ['Pulse Oximeter','Health & Pharmacy','Piece','Physical'],
            ['Glucose Meter','Health & Pharmacy','Piece','Physical'],
            ['Glucose Test Strips 25s','Health & Pharmacy','Box','Physical'],
            ['Glucose Test Strips 50s','Health & Pharmacy','Box','Physical'],
            ['Lancets 100s','Health & Pharmacy','Box','Physical'],
            ['Urine Test Strips','Health & Pharmacy','Box','Physical'],
            ['Pregnancy Test Kit','Health & Pharmacy','Piece','Physical'],
            ['Specimen Container','Health & Pharmacy','Piece','Physical'],
            ['Sterile Water for Injection','Health & Pharmacy','Bottle','Physical'],
            ['Normal Saline 0.9% 500ml','Health & Pharmacy','Bottle','Physical'],
            ["Ringer's Lactate 500ml",'Health & Pharmacy','Bottle','Physical'],
            ['Alcohol Swabs 100s','Health & Pharmacy','Box','Physical'],
            ['Baby Oral Syringe 5ml','Baby Care','Piece','Physical'],
            ['Baby Thermometer','Baby Care','Piece','Physical'],
            ['Baby Nasal Aspirator','Baby Care','Piece','Physical'],
            ['Baby Saline Drops 10ml','Baby Care','Bottle','Physical'],
            ['Baby Lotion 100ml','Baby Care','Bottle','Physical'],
            ['Baby Oil 100ml','Baby Care','Bottle','Physical'],
            ['Baby Powder 100g','Baby Care','Tin','Physical'],
            ['Baby Shampoo 100ml','Baby Care','Bottle','Physical'],
            ['Baby Soap','Baby Care','Piece','Physical'],
            ['Baby Wipes 80s','Baby Care','Pack','Physical'],
            ['Toothpaste 50g','Personal Care','Tube','Physical'],
            ['Toothpaste 100g','Personal Care','Tube','Physical'],
            ['Toothbrush Adult Soft','Personal Care','Piece','Physical'],
            ['Toothbrush Adult Medium','Personal Care','Piece','Physical'],
            ['Toothbrush Child','Personal Care','Piece','Physical'],
            ['Mouthwash 250ml','Personal Care','Bottle','Physical'],
            ['Mouthwash 500ml','Personal Care','Bottle','Physical'],
            ['Dental Floss','Personal Care','Piece','Physical'],
            ['Hand Sanitizer 100ml','Personal Care','Bottle','Physical'],
            ['Hand Sanitizer 500ml','Personal Care','Bottle','Physical'],
        ];
    }
}
