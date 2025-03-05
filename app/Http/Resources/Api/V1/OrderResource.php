<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'orderList' => $this->order_list,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'status' => $this->status,
            'userId' => $this->user_id,
            'createdAt' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => $this->when(Route::currentRouteName() == 'orders.show', Carbon::parse($this->updated_at)->format('Y-m-d H:i:s')),
            'deliveryDatetime' => $this->delivery_datetime,
        ];
    }
}
