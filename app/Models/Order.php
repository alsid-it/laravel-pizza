<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const MAX_PIZZAS_IN_ORDER = 10;
    const MAX_DRINKS_IN_ORDER = 20;

    public static function statuses(): array
    {
        return array_map(fn($case) => $case->value, OrderStatus::cases());
    }

    protected $fillable = [
        'order_list',
        'phone',
        'email',
        'address',
        'status',
        'user_id',
        'delivery_datetime',
    ];
}
