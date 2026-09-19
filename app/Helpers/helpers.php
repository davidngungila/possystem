<?php

use Illuminate\Support\Facades\Crypt;

if (! function_exists('encId')) {
    function encId($id): string
    {
        return Crypt::encryptString((string) $id);
    }
}

if (! function_exists('decId')) {
    function decId($value)
    {
        try {
            $dec = Crypt::decryptString($value);
            return $dec;
        } catch (\Throwable $e) {
            abort(404);
        }
    }
}

if (! function_exists('decIdOrRaw')) {
    function decIdOrRaw($value)
    {
        // Try encrypted first, fallback to raw numeric for backward compat
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // if value is numeric raw id, allow it (for /products/1)
            if (is_numeric($value) && (string)(int)$value === (string)$value) {
                return $value;
            }
            // also try to handle urlencoded encrypted string
            try {
                $decoded = urldecode($value);
                return Crypt::decryptString($decoded);
            } catch (\Throwable $e2) {
                abort(404);
            }
        }
    }
}

if (! function_exists('encIdForUrl')) {
    function encIdForUrl($id): string
    {
        return encId($id);
    }
}

if (! function_exists('shopSetting')) {
    function shopSetting(string $key, $default = null) {
        try {
            if (class_exists(\App\Models\Setting::class)) {
                return \App\Models\Setting::getValue($key, $default);
            }
        } catch (\Throwable $e) {}
        return $default ?? config('app.name', 'SHOP POS');
    }
}

if (! function_exists('currentShopId')) {
    function currentShopId(): ?int {
        try {
            $user = auth()->user();
            if (!$user) return null;
            // cashier is locked to their shop
            if (method_exists($user,'isCashier') && $user->isCashier()) {
                return $user->shop_id ? (int)$user->shop_id : null;
            }
            // owner/admin can switch via session — 'all' means company-wide (no filter)
            $sid = session('current_shop_id');
            if ($sid === 'all' || $sid === 0 || $sid === '0') return null;
            if ($sid) return (int)$sid;
            // if owner has no session, default to All Shops (company overview) — not first shop
            // return null for company-wide; frontend switcher will show All Shops as default
            return null;
        } catch (\Throwable $e) {}
        return null;
    }
}

if (! function_exists('currentShop')) {
    function currentShop(): ?\App\Models\Shop {
        $id = currentShopId();
        if (!$id) return null;
        try { return \App\Models\Shop::find($id); } catch (\Throwable $e) { return null; }
    }
}
if (! function_exists('isCompanyView')) {
    function isCompanyView(): bool {
        try { return session('current_shop_id') === 'all' || session('current_shop_id') === 0 || currentShopId() === null && auth()->user() && !auth()->user()->isCashier(); } catch(\Throwable $e){ return false; }
    }
}

if (! function_exists('docNextNumber')) {
    function docNextNumber(string $prefix, string $modelClass): string {
        $year = date('Y');
        $next = ((int) ($modelClass::max('id') ?? 0)) + 1;
        return strtoupper($prefix).'-'.$year.'-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('paymentMethodName')) {
    function paymentMethodName($key): string {
        return [
            'cash'=>'Cash','m-pesa'=>'M-Pesa','airtel_money'=>'Airtel Money',
            'mixx'=>'Tigo Mixx','halopesa'=>'HaloPesa','bank'=>'Bank Transfer',
            'card'=>'Card','credit'=>'Credit',
        ][$key] ?? ucwords(str_replace('_',' ', (string) $key));
    }
}

if (! function_exists('availableShops')) {
    function availableShops() {
        try {
            $user = auth()->user();
            if (!$user) return collect();
            if (method_exists($user,'isCashier') && $user->isCashier()) {
                return \App\Models\Shop::where('id', $user->shop_id)->get();
            }
            return \App\Models\Shop::where('is_active',1)->orderBy('name')->get();
        } catch (\Throwable $e) { return collect(); }
    }
}
