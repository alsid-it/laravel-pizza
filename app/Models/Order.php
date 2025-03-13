<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    const STATUS_PENDING = 'в работе';
    const STATUS_DELIVERING = 'доставляется';
    const STATUS_DELIVERED = 'доставлен';
    const STATUS_CANCELLED = 'отменен';

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
