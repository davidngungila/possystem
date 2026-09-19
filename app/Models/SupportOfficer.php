<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'phone',
    'group_name',
    'designation',
    'is_online',
    'order_index',
    'is_active',
])]
class SupportOfficer extends Model
{
    protected function casts(): array
    {
        return [
            'is_online'   => 'boolean',
            'is_active'   => 'boolean',
            'order_index' => 'integer',
        ];
    }
}
