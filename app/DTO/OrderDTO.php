<?php
namespace App\DTO;

class OrderDTO
{
    public function __construct(
        public array $orderList,
        public string $phone,
        public string $email,
        public string $address,
        public string $status,
        public int $userId,
        public string $deliveryDatetime
    ) {}

    public static function fromArray(array $data): self
    {
        // Валидация данных
        if (!isset($data['order_list'], $data['phone'], $data['email'], $data['address'], $data['status'], $data['user_id'], $data['delivery_datetime'])) {
            throw new \InvalidArgumentException('Missing required fields in data array.');
        }

        return new self(
            orderList: $data['order_list'],
            phone: $data['phone'],
            email: $data['email'],
            address: $data['address'],
            status: $data['status'],
            userId: $data['user_id'],
            deliveryDatetime: $data['delivery_datetime']
        );
    }

    public function toArray(): array
    {
        return [
            'order_list' => json_encode($this->orderList),
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'status' => $this->status,
            'user_id' => $this->userId,
            'delivery_datetime' => $this->deliveryDatetime,
        ];
    }
}
