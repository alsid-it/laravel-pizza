<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public string $validateOrderError = '';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [];

        $userId = $request->user_id;
        if ($userId) {
            $filter['user_id'] = $userId;
        }

        $ordersQuery = OrderService::getFilteredOrdersQuery($filter);

        return OrderResource::collection($ordersQuery->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $orderData = $request->all();
        $orderList = $orderData['order_list'];

        if (!OrderService::validateOrderList($orderList, $this->validateOrderError)) {
            $errorArr = [
                'message' => 'The order_list is invalid.',
                'errors' => [
                    'order_list' => [
                        'The order_list is invalid.'
                    ]
                ],
            ];

            $errorArr['validate_order_message'] = $this->validateOrderError;

            return response()->json($errorArr, 422);
        }

        $createdOrder = OrderService::createOrder($request->all());

        return new OrderResource($createdOrder);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->all());

        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->noContent();
    }
}
