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
            // Core from POS
            ['name' => 'Groceries', 'description' => 'Dry groceries & staples'],
            ['name' => 'Beverages', 'description' => 'Soft drinks, water, juices'],
            ['name' => 'Fresh Food', 'description' => 'Perishables & fresh produce'],
            ['name' => 'Dairy', 'description' => 'Milk, cheese, yogurt, butter'],
            ['name' => 'Bakery', 'description' => 'Bread, cakes, pastries'],
            ['name' => 'Meat & Seafood', 'description' => 'Meat, fish, poultry'],
            ['name' => 'Fruits & Vegetables', 'description' => 'Fresh fruits and vegetables'],
            ['name' => 'Frozen Foods', 'description' => 'Frozen items'],
            ['name' => 'Snacks & Confectionery', 'description' => 'Snacks, biscuits, sweets'],
            ['name' => 'Personal Care', 'description' => 'Cosmetics & hygiene'],
            ['name' => 'Baby Care', 'description' => 'Baby products'],
            ['name' => 'Household & Cleaning', 'description' => 'Cleaning & household'],
            ['name' => 'Health & Pharmacy', 'description' => 'Health, pharmacy, supplements'],
            ['name' => 'Electronics', 'description' => 'Small electronics & accessories'],
            ['name' => 'Stationery', 'description' => 'Office & school stationery'],
            ['name' => 'Cooking Essentials', 'description' => 'Oil, spices, cooking items'],
            ['name' => 'Alcohol & Tobacco', 'description' => 'Restricted items'],
            ['name' => 'General Merchandise', 'description' => 'Uncategorized general goods'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(
                ['slug' => Str::slug($c['name'])],
                ['name' => $c['name'], 'description' => $c['description'], 'is_active' => true]
            );
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
