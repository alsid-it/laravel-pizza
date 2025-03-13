<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'в работе';
    case DELIVERING = 'доставляется';
    case DELIVERED = 'доставлен';
    case CANCELLED = 'отменен';
}
