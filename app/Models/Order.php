<?php

namespace App\Models;

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
        return [
            self::STATUS_PENDING,
            self::STATUS_DELIVERING,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
        ];
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
