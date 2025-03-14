<?php
namespace App\DTO;

class OrderDTO
{
    public function __construct(
        public string $orderList,
        public string $phone,
        public string $email,
        public string $address,
        public string $status,
        public int $userId,
        public string $deliveryDatetime
    ) {}
}
