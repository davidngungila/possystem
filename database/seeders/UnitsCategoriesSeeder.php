<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Unit;

class UnitsCategoriesSeeder extends Seeder
{
    /**
     * Seed Units and Categories for the whole system (global, not per-shop).
     * Idempotent via firstOrCreate — safe to re-run.
     */
    public function run(): void
    {
        $categories = [
            // Core — assigned to relevant shop types
            ['name' => 'Groceries', 'description' => 'Dry groceries & staples', 'shop_types' => ['general','mini_supermarket','supermarket','grocery','wholesale']],
            ['name' => 'Beverages', 'description' => 'Soft drinks, water, juices', 'shop_types' => ['general','mini_supermarket','supermarket','beverage','grocery','restaurant','cafe','hotel']],
            ['name' => 'Fresh Food', 'description' => 'Perishables & fresh produce', 'shop_types' => ['grocery','supermarket','general']],
            ['name' => 'Dairy', 'description' => 'Milk, cheese, yogurt, butter', 'shop_types' => ['grocery','supermarket','general','bakery']],
            ['name' => 'Bakery', 'description' => 'Bread, cakes, pastries', 'shop_types' => ['bakery','supermarket','grocery','cafe','hotel']],
            ['name' => 'Meat & Seafood', 'description' => 'Meat, fish, poultry', 'shop_types' => ['butchery','grocery','supermarket','restaurant','hotel']],
            ['name' => 'Fruits & Vegetables', 'description' => 'Fresh fruits and vegetables', 'shop_types' => ['grocery','supermarket','general']],
            ['name' => 'Frozen Foods', 'description' => 'Frozen items', 'shop_types' => ['supermarket','grocery','general']],
            ['name' => 'Snacks & Confectionery', 'description' => 'Snacks, biscuits, sweets', 'shop_types' => ['general','supermarket','grocery','beverage','cafe']],
            ['name' => 'Personal Care', 'description' => 'Cosmetics & hygiene', 'shop_types' => ['general','supermarket','cosmetics','pharmacy']],
            ['name' => 'Baby Care', 'description' => 'Baby products', 'shop_types' => ['baby','pharmacy','supermarket','general']],
            ['name' => 'Household & Cleaning', 'description' => 'Cleaning & household', 'shop_types' => ['cleaning','supermarket','general']],
            ['name' => 'Health & Pharmacy', 'description' => 'Health, pharmacy, supplements', 'shop_types' => ['pharmacy']],
            ['name' => 'Medicines', 'description' => 'Prescription & OTC medicines', 'shop_types' => ['pharmacy']],
            ['name' => 'Medical Supplies', 'description' => 'Medical devices & supplies', 'shop_types' => ['pharmacy','laboratory','agrovet']],
            ['name' => 'Electronics', 'description' => 'Small electronics & accessories', 'shop_types' => ['electronics','computer','camera','gaming']],
            ['name' => 'Stationery', 'description' => 'Office & school stationery', 'shop_types' => ['stationery','bookshop']],
            ['name' => 'Cooking Essentials', 'description' => 'Oil, spices, cooking items', 'shop_types' => ['grocery','supermarket','general']],
            ['name' => 'Alcohol & Tobacco', 'description' => 'Restricted items', 'shop_types' => ['general','supermarket','beverage','restaurant','hotel']],
            ['name' => 'General Merchandise', 'description' => 'Uncategorized general goods', 'shop_types' => ['general','wholesale','supermarket']],
            // Additional to cover all 37 shop types
            ['name' => 'Clothing & Apparel', 'description' => 'Shirts, trousers, dresses', 'shop_types' => ['clothing']],
            ['name' => 'Footwear', 'description' => 'Shoes, sandals, boots', 'shop_types' => ['clothing']],
            ['name' => 'Hardware & Tools', 'description' => 'Tools, pipes, electrical items', 'shop_types' => ['hardware','building_materials','industrial']],
            ['name' => 'Cosmetics & Beauty', 'description' => 'Perfume, lotion, makeup, hair products', 'shop_types' => ['cosmetics']],
            ['name' => 'Restaurant Supplies', 'description' => 'Prepared meals, extras', 'shop_types' => ['restaurant','hotel']],
            ['name' => 'Cafe Supplies', 'description' => 'Coffee, tea, snacks', 'shop_types' => ['cafe','restaurant','hotel']],
            ['name' => 'Hotel Supplies', 'description' => 'Food, drinks, rooms/services', 'shop_types' => ['hotel']],
            ['name' => 'Agrovet Supplies', 'description' => 'Seeds, animal feed, veterinary products', 'shop_types' => ['agrovet']],
            ['name' => 'Agricultural Supplies', 'description' => 'Fertilizer, seeds, pesticides', 'shop_types' => ['agricultural','agrovet']],
            ['name' => 'Building Materials', 'description' => 'Cement, iron sheets, timber', 'shop_types' => ['building_materials','hardware','industrial']],
            ['name' => 'Auto Parts', 'description' => 'Filters, oils, spare parts', 'shop_types' => ['auto_parts','tyre','fuel']],
            ['name' => 'Tyres & Batteries', 'description' => 'Tyres, tubes, batteries', 'shop_types' => ['tyre']],
            ['name' => 'Fuel & Lubricants', 'description' => 'Petrol, diesel, lubricants', 'shop_types' => ['fuel']],
            ['name' => 'Books & Printing', 'description' => 'Books, stationery, printing services', 'shop_types' => ['bookshop']],
            ['name' => 'Furniture', 'description' => 'Chairs, tables, beds', 'shop_types' => ['furniture']],
            ['name' => 'Wholesale & Bulk', 'description' => 'Bulk products', 'shop_types' => ['wholesale','general']],
            ['name' => 'Flowers & Gifts', 'description' => 'Flowers, gifts, decorations', 'shop_types' => ['flower_gift']],
            ['name' => 'Pet Supplies', 'description' => 'Pet food, accessories', 'shop_types' => ['pet']],
            ['name' => 'Gaming & Consoles', 'description' => 'Games, consoles, accessories', 'shop_types' => ['gaming']],
            ['name' => 'Cameras & Photography', 'description' => 'Cameras, lenses, accessories', 'shop_types' => ['camera']],
            ['name' => 'Jewelry & Watches', 'description' => 'Rings, watches, necklaces', 'shop_types' => ['jewelry']],
            ['name' => 'Industrial Equipment', 'description' => 'Equipment, machinery, tools', 'shop_types' => ['industrial','hardware']],
            ['name' => 'Paint & Supplies', 'description' => 'Paint, brushes, thinners', 'shop_types' => ['paint','building_materials']],
            ['name' => 'Laboratory Supplies', 'description' => 'Lab equipment and consumables', 'shop_types' => ['laboratory']],
            ['name' => 'Fitness & Sports', 'description' => 'Gym equipment, sportswear', 'shop_types' => ['fitness']],
        ];

        foreach ($categories as $c) {
            $cat = Category::firstOrCreate(
                ['slug' => Str::slug($c['name'])],
                ['name' => $c['name'], 'description' => $c['description'], 'is_active' => true, 'shop_types' => $c['shop_types'] ?? []]
            );
            // Update shop_types if exists but empty or outdated
            if (!empty($c['shop_types']) && $cat->shop_types !== $c['shop_types']) {
                // Only update if current is null/empty or different
                if (empty($cat->shop_types) || $cat->shop_types != $c['shop_types']) {
                    $cat->shop_types = $c['shop_types'];
                    $cat->save();
                }
            }
        }

        $units = [
            ['name' => 'Piece', 'short_name' => 'pc'],
            ['name' => 'Bottle', 'short_name' => 'bt'],
            ['name' => 'Packet', 'short_name' => 'pkt'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Carton', 'short_name' => 'ctn'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'g'],
            ['name' => 'Liter', 'short_name' => 'l'],
            ['name' => 'Milliliter', 'short_name' => 'ml'],
            ['name' => 'Dozen', 'short_name' => 'dz'],
            ['name' => 'Pair', 'short_name' => 'pair'],
            ['name' => 'Set', 'short_name' => 'set'],
            ['name' => 'Roll', 'short_name' => 'roll'],
            ['name' => 'Sachet', 'short_name' => 'sachet'],
            ['name' => 'Tin', 'short_name' => 'tin'],
            ['name' => 'Can', 'short_name' => 'can'],
            ['name' => 'Bag', 'short_name' => 'bag'],
            ['name' => 'Tray', 'short_name' => 'tray'],
            ['name' => 'Meter', 'short_name' => 'm'],
            ['name' => 'Dozen Pack', 'short_name' => 'dzpk'],
        ];

        foreach ($units as $u) {
            Unit::firstOrCreate(
                ['name' => $u['name']],
                ['short_name' => $u['short_name'], 'is_active' => true]
            );
        }

        $this->command->info('Seeded '.Category::count().' categories, '.Unit::count().' units (global).');
    }
}
