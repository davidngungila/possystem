<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = ['name','code','shop_type','address','phone','email','is_active'];
    protected $casts = ['is_active'=>'boolean', 'shop_type'=>'array'];

    public static function types(): array
    {
        return [
            'general' => ['label' => 'General Shop / Duka', 'examples' => 'Sugar, rice, soap, drinks'],
            'mini_supermarket' => ['label' => 'Mini Supermarket', 'examples' => 'Food, beverages, household goods'],
            'supermarket' => ['label' => 'Supermarket', 'examples' => 'Grocery, household, personal care'],
            'pharmacy' => ['label' => 'Pharmacy', 'examples' => 'Medicines, medical supplies'],
            'clothing' => ['label' => 'Clothing Shop', 'examples' => 'Shirts, trousers, dresses, shoes'],
            'electronics' => ['label' => 'Electronics Shop', 'examples' => 'Phones, TVs, chargers, computers'],
            'computer' => ['label' => 'Computer Shop', 'examples' => 'Laptops, printers, accessories'],
            'hardware' => ['label' => 'Hardware Shop', 'examples' => 'Cement, tools, pipes, electrical items'],
            'cosmetics' => ['label' => 'Cosmetics Shop', 'examples' => 'Perfume, lotion, makeup, hair products'],
            'beverage' => ['label' => 'Beverage Shop', 'examples' => 'Soft drinks, water, juices'],
            'butchery' => ['label' => 'Butchery', 'examples' => 'Beef, chicken, goat meat'],
            'grocery' => ['label' => 'Grocery / Food Shop', 'examples' => 'Vegetables, fruits, grains'],
            'bakery' => ['label' => 'Bakery', 'examples' => 'Bread, cakes, pastries'],
            'restaurant' => ['label' => 'Restaurant', 'examples' => 'Meals, drinks, extras'],
            'cafe' => ['label' => 'Cafe', 'examples' => 'Coffee, tea, snacks'],
            'hotel' => ['label' => 'Hotel', 'examples' => 'Food, drinks, rooms/services'],
            'agrovet' => ['label' => 'Agrovet', 'examples' => 'Seeds, animal feed, veterinary products'],
            'agricultural' => ['label' => 'Agricultural Shop', 'examples' => 'Fertilizer, seeds, pesticides'],
            'building_materials' => ['label' => 'Building Materials', 'examples' => 'Cement, iron sheets, timber'],
            'auto_parts' => ['label' => 'Auto Parts', 'examples' => 'Filters, oils, spare parts'],
            'tyre' => ['label' => 'Tyre Shop', 'examples' => 'Tyres, tubes, batteries'],
            'fuel' => ['label' => 'Fuel Station', 'examples' => 'Petrol, diesel, lubricants'],
            'bookshop' => ['label' => 'Bookshop', 'examples' => 'Books, stationery, printing'],
            'stationery' => ['label' => 'Stationery', 'examples' => 'Pens, papers, files'],
            'furniture' => ['label' => 'Furniture Shop', 'examples' => 'Chairs, tables, beds'],
            'wholesale' => ['label' => 'Wholesale Shop', 'examples' => 'Bulk products'],
            'flower_gift' => ['label' => 'Flower/Gift Shop', 'examples' => 'Flowers, gifts, decorations'],
            'pet' => ['label' => 'Pet Shop', 'examples' => 'Pet food, accessories'],
            'gaming' => ['label' => 'Gaming Shop', 'examples' => 'Games, consoles, accessories'],
            'camera' => ['label' => 'Camera Shop', 'examples' => 'Cameras, lenses, accessories'],
            'jewelry' => ['label' => 'Jewelry Shop', 'examples' => 'Rings, watches, necklaces'],
            'industrial' => ['label' => 'Industrial Supply', 'examples' => 'Equipment, machinery, tools'],
            'cleaning' => ['label' => 'Cleaning Supply', 'examples' => 'Detergents, tissue, cleaning tools'],
            'baby' => ['label' => 'Baby Shop', 'examples' => 'Diapers, baby clothes, bottles'],
            'fitness' => ['label' => 'Fitness Shop', 'examples' => 'Gym equipment, sportswear'],
            'paint' => ['label' => 'Paint Shop', 'examples' => 'Paint, brushes, thinners'],
            'laboratory' => ['label' => 'Laboratory Supply', 'examples' => 'Lab equipment and consumables'],
        ];
    }

    public function shopTypesArray(): array
    {
        $val = $this->shop_type;
        if (is_array($val)) return $val;
        if (is_string($val) && $val !== '') {
            $decoded = json_decode($val, true);
            if (is_array($decoded)) return $decoded;
            return [$val];
        }
        return [];
    }

    public function shopTypeLabel(): string
    {
        $arr = $this->shopTypesArray();
        if (empty($arr)) return 'General Shop / Duka';
        $labels = array_map(fn($k) => self::types()[$k]['label'] ?? ucwords(str_replace('_',' ',$k)), $arr);
        return implode(', ', $labels);
    }

    public function shopTypeIcon(): string
    {
        return '';
    }

    public function shopTypeExamples(): string
    {
        $arr = $this->shopTypesArray();
        if (empty($arr)) return 'Sugar, rice, soap, drinks';
        $ex = array_filter(array_map(fn($k) => self::types()[$k]['examples'] ?? '', $arr));
        return implode(' | ', $ex);
    }
    public function users(){ return $this->hasMany(User::class); }
    public function products(){ return $this->hasMany(Product::class); }
    public function purchases(){ return $this->hasMany(Purchase::class); }
    public function sales(){ return $this->hasMany(Sale::class); }
    public function expenses(){ return $this->hasMany(Expense::class); }
    public function suppliers(){ return $this->hasMany(Supplier::class); }
    public function customers(){ return $this->hasMany(Customer::class); }
    public function shifts(){ return $this->hasMany(CashierShift::class, 'shop_id'); }
    public function scopeActive($q){ return $q->where('is_active',1); }
}
