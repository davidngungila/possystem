<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Shop;

class ProductCatalogueSeeder extends Seeder
{
    /**
     * Complete Product Sample Catalogue — 350+ products across 37 shop types.
     * Used as demo/sample catalogue for product registration.
     * Idempotent via SKU check.
     */
    public function run(): void
    {
        // Ensure units for catalogue exist
        $extraUnits = [
            ['name' => 'Cubic Metre', 'short_name' => 'm3'],
            ['name' => 'Sack', 'short_name' => 'sack'],
            ['name' => 'Bunch', 'short_name' => 'bunch'],
            ['name' => 'Bundle', 'short_name' => 'bundle'],
            ['name' => 'Plate', 'short_name' => 'plate'],
            ['name' => 'Cup', 'short_name' => 'cup'],
            ['name' => 'Glass', 'short_name' => 'glass'],
            ['name' => 'Night', 'short_name' => 'night'],
            ['name' => 'Session', 'short_name' => 'session'],
            ['name' => 'Service', 'short_name' => 'service'],
            ['name' => 'Trip', 'short_name' => 'trip'],
            ['name' => 'Ream', 'short_name' => 'ream'],
            ['name' => 'Jar', 'short_name' => 'jar'],
            ['name' => 'Tub', 'short_name' => 'tub'],
            ['name' => 'Pack', 'short_name' => 'pack'],
            ['name' => 'Tube', 'short_name' => 'tube'],
            ['name' => 'Sheet', 'short_name' => 'sheet'],
            ['name' => 'Bucket', 'short_name' => 'bucket'],
        ];
        foreach ($extraUnits as $u) {
            Unit::firstOrCreate(['name' => $u['name']], ['short_name' => $u['short_name'], 'is_active' => true]);
        }

        $shopTypeMap = [
            'General Shop' => 'general',
            'Mini Supermarket' => 'mini_supermarket',
            'Supermarket' => 'supermarket',
            'Pharmacy' => 'pharmacy',
            'Clothing Shop' => 'clothing',
            'Electronics Shop' => 'electronics',
            'Computer Shop' => 'computer',
            'Hardware Shop' => 'hardware',
            'Cosmetics Shop' => 'cosmetics',
            'Beverage Shop' => 'beverage',
            'Butchery' => 'butchery',
            'Grocery / Food Shop' => 'grocery',
            'Bakery' => 'bakery',
            'Restaurant' => 'restaurant',
            'Café' => 'cafe',
            'Hotel' => 'hotel',
            'Agrovet' => 'agrovet',
            'Agricultural Shop' => 'agricultural',
            'Building Materials' => 'building_materials',
            'Auto Parts' => 'auto_parts',
            'Tyre Shop' => 'tyre',
            'Fuel Station' => 'fuel',
            'Bookshop' => 'bookshop',
            'Stationery' => 'stationery',
            'Furniture Shop' => 'furniture',
            'Wholesale Shop' => 'wholesale',
            'Flower/Gift Shop' => 'flower_gift',
            'Flower / Gift Shop' => 'flower_gift',
            'Pet Shop' => 'pet',
            'Gaming Shop' => 'gaming',
            'Camera Shop' => 'camera',
            'Jewelry Shop' => 'jewelry',
            'Industrial Supply' => 'industrial',
            'Cleaning Supply' => 'cleaning',
            'Baby Shop' => 'baby',
            'Fitness Shop' => 'fitness',
            'Paint Shop' => 'paint',
            'Laboratory Supply' => 'laboratory',
            'Multi-purpose Shop' => 'general',
        ];

        $unitMap = [
            'Kg' => 'Kilogram', 'Kg.' => 'Kilogram', 'Kilo' => 'Kilogram',
            'Litre' => 'Liter', 'Litre' => 'Liter', 'L' => 'Liter', 'Litre' => 'Liter',
            'Packet' => 'Packet', 'Pack' => 'Pack', 'Box' => 'Box', 'Bottle' => 'Bottle',
            'Piece' => 'Piece', 'Roll' => 'Roll', 'Carton' => 'Carton', 'Can' => 'Can',
            'Pair' => 'Pair', 'Set' => 'Set', 'Sachet' => 'Sachet', 'Tin' => 'Tin',
            'Bag' => 'Bag', 'Tray' => 'Tray', 'Meter' => 'Meter', 'Tub' => 'Tub',
            'Jar' => 'Jar', 'Tube' => 'Tube', 'Cubic Metre' => 'Cubic Metre', 'Sack' => 'Sack',
            'Bunch' => 'Bunch', 'Bundle' => 'Bundle', 'Plate' => 'Plate', 'Cup' => 'Cup',
            'Glass' => 'Glass', 'Night' => 'Night', 'Session' => 'Session', 'Service' => 'Service',
            'Trip' => 'Trip', 'Ream' => 'Ream', 'Sheet' => 'Sheet', 'Bucket' => 'Bucket',
            'Dozen' => 'Dozen', 'Liter' => 'Liter', 'Kilogram' => 'Kilogram', 'Gram' => 'Gram',
            'Milliliter' => 'Milliliter', 'Dozen Pack' => 'Dozen Pack',
        ];

        // Helper to get unit id
        $getUnitId = function($unitName) {
            $map = [
                'Kg' => 'Kilogram', 'Kilogram' => 'Kilogram', 'Kilo' => 'Kilogram',
                'Litre' => 'Liter', 'Liter' => 'Liter', 'L' => 'Liter',
                'Packet' => 'Packet', 'Pack' => 'Pack', 'Box' => 'Box', 'Bottle' => 'Bottle',
                'Piece' => 'Piece', 'Roll' => 'Roll', 'Carton' => 'Carton', 'Can' => 'Can',
                'Pair' => 'Pair', 'Set' => 'Set', 'Sachet' => 'Sachet', 'Tin' => 'Tin',
                'Bag' => 'Bag', 'Tray' => 'Tray', 'Meter' => 'Meter', 'Tub' => 'Tub',
                'Jar' => 'Jar', 'Tube' => 'Tube', 'Cubic Metre' => 'Cubic Metre', 'Sack' => 'Sack',
                'Bunch' => 'Bunch', 'Bundle' => 'Bundle', 'Plate' => 'Plate', 'Cup' => 'Cup',
                'Glass' => 'Glass', 'Night' => 'Night', 'Session' => 'Session', 'Service' => 'Service',
                'Trip' => 'Trip', 'Ream' => 'Ream', 'Sheet' => 'Sheet', 'Bucket' => 'Bucket',
                'Dozen' => 'Dozen', 'Gram' => 'Gram', 'Kilogram' => 'Kilogram', 'Liter' => 'Liter',
            ];
            $norm = trim($unitName);
            $target = $map[$norm] ?? $norm;
            $unit = Unit::where('name', $target)->orWhere('short_name', strtolower($target))->first();
            if (!$unit) {
                $unit = Unit::firstOrCreate(['name' => $target], ['short_name' => strtolower(substr($target,0,3)), 'is_active'=>true]);
            }
            return $unit->id;
        };

        $getCategoryId = function($catName) {
            $cat = Category::where('name', $catName)->first();
            if (!$cat) {
                $cat = Category::firstOrCreate(['slug' => Str::slug($catName)], ['name'=>$catName, 'description'=>'Auto-seeded from catalogue', 'is_active'=>true]);
            }
            return $cat->id;
        };

        // Default shop for products without specific shop (use first shop or null for global)
        $defaultShopId = Shop::first()?->id;

        $catalogue = $this->catalogueData();

        $created = 0; $skipped = 0;
        foreach ($catalogue as $row) {
            [$shopTypeLabel, $productName, $categoryName, $brandName, $unitName, $productType, $taxStr] = $row;
            // Skip if product with same name already exists (idempotent)
            if (Product::where('name', $productName)->exists()) { $skipped++; continue; }

            $categoryId = $getCategoryId($categoryName);
            $unitId = $getUnitId($unitName);
            $brandId = null;
            if ($brandName && $brandName !== '—' && $brandName !== '-') {
                $brand = Brand::firstOrCreate(['slug'=>Str::slug($brandName)], ['name'=>$brandName, 'is_active'=>true]);
                $brandId = $brand->id;
            }

            $sku = strtoupper(Str::slug(substr($productName,0,12), '-')).'-'.strtoupper(Str::random(4)).rand(10,99);
            $barcode = null; // Leave blank for verification; could generate EAN-13 if needed
            // Tax
            $taxRate = 0;
            if (str_contains($taxStr, '18')) $taxRate = 18;
            elseif (str_contains($taxStr, '0')) $taxRate = 0;

            $productTypeNorm = match(strtolower($productType)) {
                'variable' => 'Variable',
                'weighted' => 'Weighted',
                'service' => 'Service',
                default => 'Physical',
            };

            // Resolve shop_id based on shop_type (first shop matching that type)
            $shopTypeKey = $shopTypeMap[$shopTypeLabel] ?? 'general';
            $shopForProduct = Shop::whereJsonContains('shop_type', $shopTypeKey)->first();
            $shopId = $shopForProduct?->id ?? $defaultShopId;
            // If no shop with that type, keep global (null) so visible to all via All Shops view
            if (!$shopForProduct) $shopId = null;

            Product::create([
                'shop_id' => $shopId,
                'name' => $productName,
                'sku' => $sku,
                'barcode' => null, // sampled products leave barcode blank for verification
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'unit_id' => $unitId,
                'supplier_id' => null,
                'buying_price' => 0,
                'selling_price' => 0,
                'wholesale_price' => null,
                'current_stock' => 0,
                'min_stock' => 5,
                'tax_rate' => $taxRate,
                'product_type' => $productTypeNorm,
                'track_stock' => true,
                'track_batch' => false,
                'track_expiry' => false,
                'status' => 'active',
                'description' => $productName.' — Sample from '. $shopTypeLabel,
            ]);
            $created++;
        }

        // Generate additional synthetic products to reach 2000+ if needed
        $targetTotal = 2000;
        $currentTotal = Product::count();
        if ($currentTotal < $targetTotal) {
            $needed = $targetTotal - $currentTotal;
            $shopTypes = array_keys(Shop::types());
            $extraAdjectives = ['Premium','Standard','Deluxe','Classic','Super','Eco','Pro','Lite','Max','Prime'];
            $extraSuffixes = ['Plus','Extra','Gold','Silver','Fresh','Natural','Organic','Family','Jumbo','Mini'];
            $perType = (int) ceil($needed / max(1, count($shopTypes)));
            $extraCreated = 0;
            foreach ($shopTypes as $typeKey) {
                $typeInfo = Shop::types()[$typeKey];
                $examples = explode(',', $typeInfo['examples'] ?? 'General');
                $baseCategory = Category::whereJsonContains('shop_types', $typeKey)->first() ?? Category::first();
                $baseUnit = Unit::first();
                for ($i = 1; $i <= $perType && ($currentTotal + $extraCreated) < $targetTotal; $i++) {
                    $adj = $extraAdjectives[array_rand($extraAdjectives)];
                    $suf = $extraSuffixes[array_rand($extraSuffixes)];
                    $catName = $baseCategory ? $baseCategory->name : 'General Merchandise';
                    $productName = $typeInfo['label'].' '.$adj.' '.$catName.' '.$suf.' '.$i;
                    // Ensure unique name
                    if (Product::where('name', $productName)->exists()) continue;
                    $sku = strtoupper(Str::slug(substr($productName,0,10), '-')).'-'.strtoupper(Str::random(3)).rand(100,999);
                    $shopForExtra = Shop::whereJsonContains('shop_type', $typeKey)->first();
                    $shopIdExtra = $shopForExtra?->id ?? $defaultShopId;
                    if (!$shopForExtra) $shopIdExtra = null;
                    // Pick a category for this shop type
                    $catForType = Category::whereJsonContains('shop_types', $typeKey)->inRandomOrder()->first() ?? $baseCategory;
                    $unitForExtra = Unit::inRandomOrder()->first() ?? $baseUnit;
                    Product::create([
                        'shop_id' => $shopIdExtra,
                        'name' => $productName,
                        'sku' => $sku,
                        'barcode' => null,
                        'category_id' => $catForType?->id,
                        'brand_id' => null,
                        'unit_id' => $unitForExtra?->id,
                        'supplier_id' => null,
                        'buying_price' => rand(500, 5000),
                        'selling_price' => rand(1000, 8000),
                        'wholesale_price' => null,
                        'current_stock' => 0,
                        'min_stock' => 5,
                        'tax_rate' => 18,
                        'product_type' => 'Physical',
                        'track_stock' => true,
                        'track_batch' => false,
                        'track_expiry' => false,
                        'status' => 'active',
                        'description' => $productName.' — Auto-generated sample for '.$typeInfo['label'],
                    ]);
                    $extraCreated++;
                }
            }
            $this->command->info("Generated $extraCreated additional synthetic products to reach 1000+.");
            $created += $extraCreated;
        }

        $this->command->info("Product catalogue seeded: $created created, $skipped skipped (already exists). Total products: ".Product::count());
    }

    private function catalogueData(): array
    {
        // [Shop Type, Product Name, Category, Brand, Unit, Product Type, Tax%]
        // Comprehensive 350 from user table
        return [
            ['General Shop','Sugar 1kg','Groceries','—','Kg','Physical','0/18'],
            ['General Shop','Rice 1kg','Groceries','—','Kg','Physical','0/18'],
            ['General Shop','Maize Flour 1kg','Groceries','—','Kg','Physical','0/18'],
            ['General Shop','Wheat Flour 1kg','Groceries','—','Kg','Physical','0/18'],
            ['General Shop','Beans 1kg','Groceries','—','Kg','Physical','0/18'],
            ['General Shop','Cooking Oil 1L','Cooking Essentials','—','Litre','Physical','18'],
            ['General Shop','Salt 500g','Groceries','—','Packet','Physical','0/18'],
            ['General Shop','Tea Leaves','Beverages','—','Packet','Physical','18'],
            ['General Shop','Coffee','Beverages','—','Packet','Physical','18'],
            ['General Shop','Pasta','Groceries','—','Packet','Physical','18'],
            ['General Shop','Biscuits','Snacks & Confectionery','—','Packet','Physical','18'],
            ['General Shop','Bread','Bakery','—','Piece','Physical','18'],
            ['General Shop','Tomato Sauce','Groceries','—','Bottle','Physical','18'],
            ['General Shop','Laundry Detergent','Household & Cleaning','—','Kg','Physical','18'],
            ['General Shop','Dishwashing Liquid','Household & Cleaning','—','Bottle','Physical','18'],
            ['General Shop','Toilet Tissue','Household & Cleaning','—','Roll','Physical','18'],
            ['General Shop','Bottled Water 500ml','Beverages','—','Bottle','Physical','18'],
            ['General Shop','Soda 500ml','Beverages','—','Bottle','Physical','18'],
            ['General Shop','Juice 500ml','Beverages','—','Bottle','Physical','18'],
            ['General Shop','Milk 1L','Dairy','—','Litre','Physical','0/18'],
            ['General Shop','Yoghurt','Dairy','—','Bottle','Physical','0/18'],
            ['Mini Supermarket','Breakfast Cereal','Groceries','—','Box','Physical','18'],
            ['Mini Supermarket','Peanut Butter','Groceries','—','Jar','Physical','18'],
            ['Mini Supermarket','Mayonnaise','Groceries','—','Bottle','Physical','18'],
            ['Mini Supermarket','Cheese','Dairy','—','Kg','Physical','0/18'],
            ['Mini Supermarket','Frozen Chicken','Frozen Foods','—','Kg','Physical','18'],
            ['Mini Supermarket','Frozen Fish','Frozen Foods','—','Kg','Physical','18'],
            ['Mini Supermarket','Fresh Tomatoes','Fruits & Vegetables','—','Kg','Physical','0/18'],
            ['Mini Supermarket','Potatoes','Fruits & Vegetables','—','Kg','Physical','0/18'],
            ['Mini Supermarket','Diapers','Baby Care','—','Pack','Physical','18'],
            ['Mini Supermarket','Baby Wipes','Baby Care','—','Pack','Physical','18'],
            ['Mini Supermarket','Shampoo','Personal Care','—','Bottle','Physical','18'],
            ['Supermarket','Ice Cream','Frozen Foods','—','Tub','Physical','18'],
            ['Supermarket','Frozen Chips','Frozen Foods','—','Kg','Physical','18'],
            ['Supermarket','Butter','Dairy','—','Pack','Physical','0/18'],
            ['Supermarket','Cooking Spices','Cooking Essentials','—','Packet','Physical','18'],
            ['Supermarket','Pet Food','General Merchandise','—','Kg','Physical','18'],
            ['Pharmacy','Paracetamol Tablets','Health & Pharmacy','—','Pack','Physical','—'],
            ['Pharmacy','Ibuprofen Tablets','Health & Pharmacy','—','Pack','Physical','—'],
            ['Pharmacy','Cetirizine Tablets','Health & Pharmacy','—','Pack','Physical','—'],
            ['Pharmacy','Loratadine Tablets','Health & Pharmacy','—','Pack','Physical','—'],
            ['Pharmacy','Omeprazole Capsules','Health & Pharmacy','—','Pack','Physical','—'],
            ['Pharmacy','ORS Sachets','Health & Pharmacy','—','Sachet','Physical','—'],
            ['Pharmacy','Antacid','Health & Pharmacy','—','Bottle','Physical','—'],
            ['Pharmacy','Cough Syrup','Health & Pharmacy','—','Bottle','Physical','—'],
            ['Pharmacy','Multivitamins','Health & Pharmacy','—','Bottle','Physical','—'],
            ['Pharmacy','Thermometer','Health & Pharmacy','—','Piece','Physical','—'],
            ['Pharmacy','Blood Pressure Monitor','Health & Pharmacy','—','Piece','Physical','—'],
            ['Pharmacy','Glucose Meter','Health & Pharmacy','—','Piece','Physical','—'],
            ['Pharmacy','Glucose Test Strips','Health & Pharmacy','—','Pack','Physical','—'],
            ['Pharmacy','Surgical Gloves','Health & Pharmacy','—','Box','Physical','—'],
            ['Pharmacy','Face Masks','Health & Pharmacy','—','Box','Physical','—'],
            ['Pharmacy','Syringes','Health & Pharmacy','—','Box','Physical','—'],
            ['Pharmacy','Cotton Wool','Health & Pharmacy','—','Pack','Physical','—'],
            ['Pharmacy','Bandages','Health & Pharmacy','—','Pack','Physical','—'],
            ['Pharmacy','Plasters','Health & Pharmacy','—','Box','Physical','—'],
            ['Clothing Shop','Men\'s T-Shirt','General Merchandise','—','Piece','Variable','18'],
            ['Clothing Shop','Men\'s Shirt','General Merchandise','—','Piece','Variable','18'],
            ['Clothing Shop','Men\'s Trousers','General Merchandise','—','Piece','Variable','18'],
            ['Clothing Shop','Men\'s Jeans','General Merchandise','—','Piece','Variable','18'],
            ['Clothing Shop','Women\'s Dress','General Merchandise','—','Piece','Variable','18'],
            ['Clothing Shop','Women\'s Blouse','General Merchandise','—','Piece','Variable','18'],
            ['Clothing Shop','Women\'s Skirt','General Merchandise','—','Piece','Variable','18'],
            ['Clothing Shop','Children\'s T-Shirt','General Merchandise','—','Piece','Variable','18'],
            ['Clothing Shop','School Uniform','General Merchandise','—','Set','Variable','18'],
            ['Clothing Shop','Sneakers','General Merchandise','—','Pair','Variable','18'],
            ['Clothing Shop','Formal Shoes','General Merchandise','—','Pair','Variable','18'],
            ['Clothing Shop','Sandals','General Merchandise','—','Pair','Variable','18'],
            ['Electronics Shop','Smartphone','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Feature Phone','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Tablet','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Television','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Bluetooth Speaker','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Earphones','Electronics','—','Pair','Physical','18'],
            ['Electronics Shop','Wireless Earbuds','Electronics','—','Pair','Physical','18'],
            ['Electronics Shop','Phone Charger','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','USB Cable','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Power Bank','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Phone Case','Electronics','—','Piece','Variable','18'],
            ['Electronics Shop','Screen Protector','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Memory Card','Electronics','—','Piece','Physical','18'],
            ['Electronics Shop','Remote Control','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Laptop','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Desktop Computer','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Monitor','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Keyboard','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Mouse','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Laptop Bag','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Laptop Charger','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Webcam','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Headset','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','USB Hub','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Flash Disk','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','External HDD','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','SSD','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','RAM','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Printer','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Barcode Scanner','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Barcode Printer','Electronics','—','Piece','Physical','18'],
            ['Computer Shop','Thermal Printer','Electronics','—','Piece','Physical','18'],
            ['Hardware Shop','Cement 50kg','General Merchandise','—','Bag','Physical','18'],
            ['Hardware Shop','Sand','General Merchandise','—','Cubic Metre','Physical','18'],
            ['Hardware Shop','Ballast','General Merchandise','—','Cubic Metre','Physical','18'],
            ['Hardware Shop','Bricks','General Merchandise','—','Piece','Physical','18'],
            ['Hardware Shop','Iron Bars','General Merchandise','—','Piece','Physical','18'],
            ['Hardware Shop','Binding Wire','General Merchandise','—','Kg','Physical','18'],
            ['Hardware Shop','Roofing Sheets','General Merchandise','—','Piece','Physical','18'],
            ['Hardware Shop','Roofing Nails','General Merchandise','—','Kg','Physical','18'],
            ['Hardware Shop','Hammer','General Merchandise','—','Piece','Physical','18'],
            ['Hardware Shop','Screwdriver','General Merchandise','—','Piece','Physical','18'],
            ['Cosmetics Shop','Body Lotion','Personal Care','—','Bottle','Physical','18'],
            ['Cosmetics Shop','Body Oil','Personal Care','—','Bottle','Physical','18'],
            ['Cosmetics Shop','Petroleum Jelly','Personal Care','—','Jar','Physical','18'],
            ['Cosmetics Shop','Face Cream','Personal Care','—','Tube','Physical','18'],
            ['Cosmetics Shop','Sunscreen','Personal Care','—','Bottle','Physical','18'],
            ['Cosmetics Shop','Perfume','Personal Care','—','Bottle','Physical','18'],
            ['Cosmetics Shop','Body Spray','Personal Care','—','Bottle','Physical','18'],
            ['Cosmetics Shop','Deodorant','Personal Care','—','Piece','Physical','18'],
            ['Cosmetics Shop','Shampoo','Personal Care','—','Bottle','Physical','18'],
            ['Cosmetics Shop','Conditioner','Personal Care','—','Bottle','Physical','18'],
            ['Cosmetics Shop','Hair Oil','Personal Care','—','Bottle','Physical','18'],
            ['Cosmetics Shop','Hair Gel','Personal Care','—','Jar','Physical','18'],
            ['Cosmetics Shop','Lipstick','Personal Care','—','Piece','Variable','18'],
            ['Cosmetics Shop','Nail Polish','Personal Care','—','Bottle','Variable','18'],
            ['Beverage Shop','Bottled Water 500ml','Beverages','—','Bottle','Physical','18'],
            ['Beverage Shop','Soda 500ml','Beverages','—','Bottle','Physical','18'],
            ['Beverage Shop','Soda Can 330ml','Beverages','—','Can','Physical','18'],
            ['Beverage Shop','Energy Drink','Beverages','—','Can','Physical','18'],
            ['Beverage Shop','Juice','Beverages','—','Bottle','Physical','18'],
            ['Beverage Shop','Milk','Dairy','—','Litre','Physical','0/18'],
            ['Beverage Shop','Yoghurt','Dairy','—','Bottle','Physical','0/18'],
            ['Butchery','Beef','Meat & Seafood','—','Kg','Weighted','18'],
            ['Butchery','Goat Meat','Meat & Seafood','—','Kg','Weighted','18'],
            ['Butchery','Chicken','Meat & Seafood','—','Kg','Weighted','18'],
            ['Butchery','Pork','Meat & Seafood','—','Kg','Weighted','18'],
            ['Butchery','Liver','Meat & Seafood','—','Kg','Weighted','18'],
            ['Butchery','Minced Meat','Meat & Seafood','—','Kg','Weighted','18'],
            ['Butchery','Steak','Meat & Seafood','—','Kg','Weighted','18'],
            ['Butchery','Beef Ribs','Meat & Seafood','—','Kg','Weighted','18'],
            ['Butchery','Sausages','Meat & Seafood','—','Kg','Weighted','18'],
            ['Grocery / Food Shop','Tomatoes','Fresh Food','—','Kg','Weighted','0/18'],
            ['Grocery / Food Shop','Onions','Fresh Food','—','Kg','Weighted','0/18'],
            ['Grocery / Food Shop','Potatoes','Fresh Food','—','Kg','Weighted','0/18'],
            ['Grocery / Food Shop','Cabbage','Fresh Food','—','Piece','Physical','0/18'],
            ['Grocery / Food Shop','Spinach','Fresh Food','—','Bundle','Physical','0/18'],
            ['Grocery / Food Shop','Bananas','Fresh Food','—','Bunch','Physical','0/18'],
            ['Grocery / Food Shop','Mangoes','Fresh Food','—','Kg','Weighted','0/18'],
            ['Grocery / Food Shop','Apples','Fresh Food','—','Kg','Weighted','0/18'],
            ['Bakery','White Bread','Bakery','—','Piece','Physical','18'],
            ['Bakery','Brown Bread','Bakery','—','Piece','Physical','18'],
            ['Bakery','Buns','Bakery','—','Piece','Physical','18'],
            ['Bakery','Doughnut','Bakery','—','Piece','Physical','18'],
            ['Bakery','Cupcake','Bakery','—','Piece','Physical','18'],
            ['Bakery','Birthday Cake','Bakery','—','Piece','Physical','18'],
            ['Bakery','Meat Pie','Bakery','—','Piece','Physical','18'],
            ['Bakery','Samosa','Bakery','—','Piece','Physical','18'],
            ['Bakery','Cookies','Bakery','—','Packet','Physical','18'],
            ['Bakery','Croissant','Bakery','—','Piece','Physical','18'],
            ['Restaurant','Pilau','General Merchandise','—','Plate','Service','18'],
            ['Restaurant','Chips','General Merchandise','—','Plate','Service','18'],
            ['Restaurant','Chips Mayai','General Merchandise','—','Plate','Service','18'],
            ['Restaurant','Rice & Beef','General Merchandise','—','Plate','Service','18'],
            ['Restaurant','Rice & Chicken','General Merchandise','—','Plate','Service','18'],
            ['Restaurant','Ugali & Fish','General Merchandise','—','Plate','Service','18'],
            ['Restaurant','Burger','General Merchandise','—','Piece','Service','18'],
            ['Restaurant','Pizza','General Merchandise','—','Piece','Service','18'],
            ['Café','Espresso','Beverages','—','Cup','Service','18'],
            ['Café','Cappuccino','Beverages','—','Cup','Service','18'],
            ['Café','Latte','Beverages','—','Cup','Service','18'],
            ['Café','Fresh Juice','Beverages','—','Glass','Service','18'],
            ['Café','Cake Slice','Bakery','—','Piece','Service','18'],
            ['Café','Sandwich','Bakery','—','Piece','Service','18'],
            ['Hotel','Standard Room','General Merchandise','—','Night','Service','18'],
            ['Hotel','Deluxe Room','General Merchandise','—','Night','Service','18'],
            ['Hotel','Executive Room','General Merchandise','—','Night','Service','18'],
            ['Hotel','Conference Room','General Merchandise','—','Session','Service','18'],
            ['Hotel','Laundry Service','General Merchandise','—','Service','Service','18'],
            ['Hotel','Airport Transfer','General Merchandise','—','Trip','Service','18'],
            ['Agrovet','Maize Seeds','General Merchandise','—','Packet','Physical','—'],
            ['Agrovet','Bean Seeds','General Merchandise','—','Packet','Physical','—'],
            ['Agrovet','Vegetable Seeds','General Merchandise','—','Packet','Physical','—'],
            ['Agrovet','Animal Feed','General Merchandise','—','Bag','Physical','—'],
            ['Agrovet','Poultry Feed','General Merchandise','—','Bag','Physical','—'],
            ['Agrovet','Mineral Supplement','General Merchandise','—','Kg','Physical','—'],
            ['Agrovet','Farm Sprayer','General Merchandise','—','Piece','Physical','18'],
            ['Agrovet','Watering Can','General Merchandise','—','Piece','Physical','18'],
            ['Agricultural Shop','Fertilizer','General Merchandise','—','Bag','Physical','—'],
            ['Agricultural Shop','Compost','General Merchandise','—','Bag','Physical','—'],
            ['Agricultural Shop','Hoe','General Merchandise','—','Piece','Physical','18'],
            ['Agricultural Shop','Panga','General Merchandise','—','Piece','Physical','18'],
            ['Agricultural Shop','Wheelbarrow','General Merchandise','—','Piece','Physical','18'],
            ['Building Materials','Cement 50kg','General Merchandise','—','Bag','Physical','18'],
            ['Building Materials','Iron Sheets','General Merchandise','—','Piece','Physical','18'],
            ['Building Materials','Timber','General Merchandise','—','Piece','Physical','18'],
            ['Building Materials','Plywood','General Merchandise','—','Sheet','Physical','18'],
            ['Building Materials','Nails','General Merchandise','—','Kg','Physical','18'],
            ['Building Materials','Steel Bars','General Merchandise','—','Piece','Physical','18'],
            ['Building Materials','Doors','General Merchandise','—','Piece','Physical','18'],
            ['Building Materials','Windows','General Merchandise','—','Piece','Physical','18'],
            ['Building Materials','Paint','General Merchandise','—','Bucket','Physical','18'],
            ['Auto Parts','Engine Oil','General Merchandise','—','Litre','Physical','18'],
            ['Auto Parts','Oil Filter','General Merchandise','—','Piece','Physical','18'],
            ['Auto Parts','Air Filter','General Merchandise','—','Piece','Physical','18'],
            ['Auto Parts','Fuel Filter','General Merchandise','—','Piece','Physical','18'],
            ['Auto Parts','Brake Pads','General Merchandise','—','Set','Physical','18'],
            ['Auto Parts','Brake Disc','General Merchandise','—','Piece','Physical','18'],
            ['Auto Parts','Brake Fluid','General Merchandise','—','Bottle','Physical','18'],
            ['Auto Parts','Car Battery','General Merchandise','—','Piece','Physical','18'],
            ['Auto Parts','Spark Plug','General Merchandise','—','Piece','Physical','18'],
            ['Auto Parts','Wiper Blade','General Merchandise','—','Piece','Physical','18'],
            ['Tyre Shop','Motorcycle Tyre','General Merchandise','—','Piece','Variable','18'],
            ['Tyre Shop','Car Tyre','General Merchandise','—','Piece','Variable','18'],
            ['Tyre Shop','SUV Tyre','General Merchandise','—','Piece','Variable','18'],
            ['Tyre Shop','Truck Tyre','General Merchandise','—','Piece','Variable','18'],
            ['Tyre Shop','Motorcycle Tube','General Merchandise','—','Piece','Variable','18'],
            ['Tyre Shop','Wheel Rim','General Merchandise','—','Piece','Variable','18'],
            ['Tyre Shop','Wheel Nut','General Merchandise','—','Piece','Physical','18'],
            ['Tyre Shop','Tyre Repair Kit','General Merchandise','—','Set','Physical','18'],
            ['Fuel Station','Petrol','General Merchandise','—','Litre','Physical','—'],
            ['Fuel Station','Diesel','General Merchandise','—','Litre','Physical','—'],
            ['Fuel Station','Engine Oil','General Merchandise','—','Litre','Physical','18'],
            ['Fuel Station','Gear Oil','General Merchandise','—','Litre','Physical','18'],
            ['Fuel Station','Coolant','General Merchandise','—','Litre','Physical','18'],
            ['Bookshop','Mathematics Textbook','Stationery','—','Piece','Physical','18'],
            ['Bookshop','English Textbook','Stationery','—','Piece','Physical','18'],
            ['Bookshop','Dictionary','Stationery','—','Piece','Physical','18'],
            ['Bookshop','Novel','Stationery','—','Piece','Physical','18'],
            ['Bookshop','Exercise Book','Stationery','—','Piece','Physical','18'],
            ['Bookshop','Notebook','Stationery','—','Piece','Physical','18'],
            ['Stationery','Ball Pen','Stationery','—','Piece','Physical','18'],
            ['Stationery','Pencil','Stationery','—','Piece','Physical','18'],
            ['Stationery','Eraser','Stationery','—','Piece','Physical','18'],
            ['Stationery','Sharpener','Stationery','—','Piece','Physical','18'],
            ['Stationery','Ruler','Stationery','—','Piece','Physical','18'],
            ['Stationery','A4 Paper','Stationery','—','Ream','Physical','18'],
            ['Stationery','File','Stationery','—','Piece','Physical','18'],
            ['Stationery','Stapler','Stationery','—','Piece','Physical','18'],
            ['Furniture Shop','Office Chair','General Merchandise','—','Piece','Physical','18'],
            ['Furniture Shop','Office Desk','General Merchandise','—','Piece','Physical','18'],
            ['Furniture Shop','Dining Table','General Merchandise','—','Piece','Physical','18'],
            ['Furniture Shop','Bed','General Merchandise','—','Piece','Physical','18'],
            ['Furniture Shop','Mattress','General Merchandise','—','Piece','Physical','18'],
            ['Furniture Shop','Wardrobe','General Merchandise','—','Piece','Physical','18'],
            ['Furniture Shop','Sofa','General Merchandise','—','Set','Physical','18'],
            ['Furniture Shop','Cabinet','General Merchandise','—','Piece','Physical','18'],
            ['Wholesale Shop','Soda Carton','Beverages','—','Carton','Physical','18'],
            ['Wholesale Shop','Water Carton','Beverages','—','Carton','Physical','18'],
            ['Wholesale Shop','Biscuits Carton','Snacks & Confectionery','—','Carton','Physical','18'],
            ['Wholesale Shop','Soap Carton','Personal Care','—','Carton','Physical','18'],
            ['Wholesale Shop','Sugar Sack','Groceries','—','Sack','Physical','0/18'],
            ['Flower/Gift Shop','Rose','General Merchandise','—','Piece','Physical','18'],
            ['Flower/Gift Shop','Bouquet','General Merchandise','—','Bundle','Physical','18'],
            ['Flower/Gift Shop','Gift Box','General Merchandise','—','Piece','Physical','18'],
            ['Flower/Gift Shop','Greeting Card','Stationery','—','Piece','Physical','18'],
            ['Flower/Gift Shop','Teddy Bear','General Merchandise','—','Piece','Physical','18'],
            ['Flower/Gift Shop','Photo Frame','General Merchandise','—','Piece','Physical','18'],
            ['Pet Shop','Dog Food','General Merchandise','—','Kg','Physical','18'],
            ['Pet Shop','Cat Food','General Merchandise','—','Kg','Physical','18'],
            ['Pet Shop','Fish Food','General Merchandise','—','Packet','Physical','18'],
            ['Pet Shop','Dog Shampoo','Personal Care','—','Bottle','Physical','18'],
            ['Pet Shop','Pet Collar','General Merchandise','—','Piece','Physical','18'],
            ['Pet Shop','Pet Leash','General Merchandise','—','Piece','Physical','18'],
            ['Gaming Shop','PlayStation Console','Electronics','—','Piece','Physical','18'],
            ['Gaming Shop','Xbox Console','Electronics','—','Piece','Physical','18'],
            ['Gaming Shop','Game Controller','Electronics','—','Piece','Physical','18'],
            ['Gaming Shop','Gaming Headset','Electronics','—','Piece','Physical','18'],
            ['Gaming Shop','Gaming Keyboard','Electronics','—','Piece','Physical','18'],
            ['Gaming Shop','Gaming Mouse','Electronics','—','Piece','Physical','18'],
            ['Camera Shop','DSLR Camera','Electronics','—','Piece','Physical','18'],
            ['Camera Shop','Mirrorless Camera','Electronics','—','Piece','Physical','18'],
            ['Camera Shop','Camera Lens','Electronics','—','Piece','Physical','18'],
            ['Camera Shop','Camera Bag','Electronics','—','Piece','Physical','18'],
            ['Camera Shop','Tripod','Electronics','—','Piece','Physical','18'],
            ['Camera Shop','Memory Card','Electronics','—','Piece','Physical','18'],
            ['Camera Shop','Camera Battery','Electronics','—','Piece','Physical','18'],
            ['Jewelry Shop','Ring','General Merchandise','—','Piece','Variable','18'],
            ['Jewelry Shop','Necklace','General Merchandise','—','Piece','Variable','18'],
            ['Jewelry Shop','Bracelet','General Merchandise','—','Piece','Variable','18'],
            ['Jewelry Shop','Earrings','General Merchandise','—','Pair','Variable','18'],
            ['Jewelry Shop','Pendant','General Merchandise','—','Piece','Variable','18'],
            ['Jewelry Shop','Chain','General Merchandise','—','Piece','Variable','18'],
            ['Jewelry Shop','Watch','General Merchandise','—','Piece','Physical','18'],
            ['Industrial Supply','Welding Machine','General Merchandise','—','Piece','Physical','18'],
            ['Industrial Supply','Welding Electrodes','General Merchandise','—','Box','Physical','18'],
            ['Industrial Supply','Safety Helmet','General Merchandise','—','Piece','Physical','18'],
            ['Industrial Supply','Safety Boots','General Merchandise','—','Pair','Variable','18'],
            ['Industrial Supply','Drill Machine','General Merchandise','—','Piece','Physical','18'],
            ['Industrial Supply','Angle Grinder','General Merchandise','—','Piece','Physical','18'],
            ['Industrial Supply','Air Compressor','General Merchandise','—','Piece','Physical','18'],
            ['Industrial Supply','Water Pump','General Merchandise','—','Piece','Physical','18'],
            ['Cleaning Supply','Laundry Detergent','Household & Cleaning','—','Kg','Physical','18'],
            ['Cleaning Supply','Dishwashing Liquid','Household & Cleaning','—','Litre','Physical','18'],
            ['Cleaning Supply','Bleach','Household & Cleaning','—','Litre','Physical','18'],
            ['Cleaning Supply','Floor Cleaner','Household & Cleaning','—','Litre','Physical','18'],
            ['Cleaning Supply','Toilet Cleaner','Household & Cleaning','—','Bottle','Physical','18'],
            ['Cleaning Supply','Glass Cleaner','Household & Cleaning','—','Bottle','Physical','18'],
            ['Cleaning Supply','Disinfectant','Household & Cleaning','—','Litre','Physical','18'],
            ['Cleaning Supply','Hand Sanitizer','Personal Care','—','Bottle','Physical','18'],
            ['Cleaning Supply','Broom','Household & Cleaning','—','Piece','Physical','18'],
            ['Cleaning Supply','Mop','Household & Cleaning','—','Piece','Physical','18'],
            ['Baby Shop','Baby Diapers','Baby Care','—','Pack','Variable','18'],
            ['Baby Shop','Baby Wipes','Baby Care','—','Pack','Physical','18'],
            ['Baby Shop','Baby Lotion','Baby Care','—','Bottle','Physical','18'],
            ['Baby Shop','Baby Oil','Baby Care','—','Bottle','Physical','18'],
            ['Baby Shop','Baby Powder','Baby Care','—','Bottle','Physical','18'],
            ['Baby Shop','Baby Shampoo','Baby Care','—','Bottle','Physical','18'],
            ['Baby Shop','Baby Clothes','Baby Care','—','Piece','Variable','18'],
            ['Baby Shop','Baby Shoes','Baby Care','—','Pair','Variable','18'],
            ['Baby Shop','Baby Bottle','Baby Care','—','Piece','Physical','18'],
            ['Baby Shop','Pacifier','Baby Care','—','Piece','Physical','18'],
            ['Fitness Shop','Dumbbell','General Merchandise','—','Piece','Variable','18'],
            ['Fitness Shop','Barbell','General Merchandise','—','Piece','Physical','18'],
            ['Fitness Shop','Weight Plate','General Merchandise','—','Piece','Variable','18'],
            ['Fitness Shop','Kettlebell','General Merchandise','—','Piece','Variable','18'],
            ['Fitness Shop','Yoga Mat','General Merchandise','—','Piece','Physical','18'],
            ['Fitness Shop','Resistance Band','General Merchandise','—','Piece','Physical','18'],
            ['Fitness Shop','Skipping Rope','General Merchandise','—','Piece','Physical','18'],
            ['Fitness Shop','Sports Shoes','General Merchandise','—','Pair','Variable','18'],
            ['Paint Shop','Interior Emulsion Paint','General Merchandise','—','Bucket','Physical','18'],
            ['Paint Shop','Exterior Paint','General Merchandise','—','Bucket','Physical','18'],
            ['Paint Shop','Gloss Paint','General Merchandise','—','Tin','Physical','18'],
            ['Paint Shop','Primer','General Merchandise','—','Tin','Physical','18'],
            ['Paint Shop','Paint Brush','General Merchandise','—','Piece','Physical','18'],
            ['Paint Shop','Paint Roller','General Merchandise','—','Piece','Physical','18'],
            ['Paint Shop','Sandpaper','General Merchandise','—','Sheet','Physical','18'],
            ['Paint Shop','Paint Thinner','General Merchandise','—','Litre','Physical','18'],
            ['Laboratory Supply','Microscope','General Merchandise','—','Piece','Physical','18'],
            ['Laboratory Supply','Centrifuge','General Merchandise','—','Piece','Physical','18'],
            ['Laboratory Supply','Laboratory Balance','General Merchandise','—','Piece','Physical','18'],
            ['Laboratory Supply','Test Tube','General Merchandise','—','Piece','Physical','18'],
            ['Laboratory Supply','Beaker','General Merchandise','—','Piece','Physical','18'],
            ['Laboratory Supply','Measuring Cylinder','General Merchandise','—','Piece','Physical','18'],
            ['Laboratory Supply','Petri Dish','General Merchandise','—','Piece','Physical','18'],
            ['Laboratory Supply','Pipette','General Merchandise','—','Piece','Physical','18'],
            ['Laboratory Supply','Laboratory Gloves','General Merchandise','—','Box','Physical','18'],
            ['Laboratory Supply','Laboratory Coat','General Merchandise','—','Piece','Variable','18'],
            ['Multi-purpose Shop','Sugar','Groceries','—','Kg','Physical','0/18'],
            ['Multi-purpose Shop','Rice','Groceries','—','Kg','Physical','0/18'],
            ['Multi-purpose Shop','Soda','Beverages','—','Bottle','Physical','18'],
            ['Multi-purpose Shop','Soap','Personal Care','—','Piece','Physical','18'],
            ['Multi-purpose Shop','Detergent','Household & Cleaning','—','Kg','Physical','18'],
            ['Multi-purpose Shop','Diapers','Baby Care','—','Pack','Variable','18'],
            ['Multi-purpose Shop','Toothpaste','Personal Care','—','Tube','Physical','18'],
            ['Multi-purpose Shop','USB Cable','Electronics','—','Piece','Physical','18'],
            ['Multi-purpose Shop','Notebook','Stationery','—','Piece','Physical','18'],
            ['Multi-purpose Shop','T-Shirt','General Merchandise','—','Piece','Variable','18'],
            ['Multi-purpose Shop','Cement','General Merchandise','—','Bag','Physical','18'],
            ['Multi-purpose Shop','Paint','General Merchandise','—','Bucket','Physical','18'],
            ['Multi-purpose Shop','Cooking Oil','Cooking Essentials','—','Litre','Physical','18'],
            ['Multi-purpose Shop','Phone Charger','Electronics','—','Piece','Physical','18'],
        ];
    }
}
