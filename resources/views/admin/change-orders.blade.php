<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменение статуса заказов</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #e74c3c;
        }

        .order-container {
            margin: 20px 0;
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .order-status {
            color: #4CAF50; /* Цвет для успешного статуса */
            font-weight: bold;
        }

        .order-details {
            margin-top: 10px;
        }

        .item-list {
            margin: 10px 0;
            padding: 0;
            list-style-type: none;
        }

        .item-list li {
            margin-bottom: 5px;
        }

        .address {
            font-style: italic;
        }

        .notification {
            background-color: #f44336; /* Красный цвет для ошибок */
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }

        .notification.success {
            background-color: #45a049;
        }

        .pizza-icon {
            position: absolute;
            top: 30px;
            right: 43px;
            cursor: pointer;
            width: 40px;
            height: 40px;
        }

        .status-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-form select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .status-form button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .status-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<a href="{{ route('showcase') }}">
    <img src="{{ asset('images/pizza.png') }}" alt="Домик" class="pizza-icon">
</a>

<h1>Изменение статуса заказов</h1>

@if(session('success'))
    <div class="notification success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="notification">
        {{ session('error') }}
    </div>
@endif

<!-- Перебираем заказы и выводим их -->
@foreach($orders as $order)
    <div class="order-container">
        <div class="order-header">
            <span>Заказ #{{ $order['id'] }}</span>
            <form class="status-form" action="{{ route('admin.order-statuses') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order['id'] }}">
                <select name="status">
                    @foreach(App\Models\Order::statuses() as $status)
                        <option value="{{ $status }}" {{ $order['status'] == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                <button type="submit">Изменить</button>
            </form>
        </div>
        <div class="order-details">
            <p><strong>Дата заказа:</strong> {{ \Carbon\Carbon::parse($order['order_date'])->format('d.m.Y') }}</p>
            <p><strong>Время доставки:</strong> {{ \Carbon\Carbon::parse($order['delivery_time'])->format('H:i') }}</p>
            <p><strong>Адрес доставки:</strong> <span class="address">{{ $order['address'] }}</span></p>

            <strong>Список заказанного:</strong>
            <ul class="item-list">
                @isset($order['order_list']['pizzas'])
                    @foreach($order['order_list']['pizzas'] as $pizza)
                        <li>{{ $pizza['name'] }} x {{ $pizza['quantity'] }}</li>
                    @endforeach
                @endisset
                @isset($order['order_list']['drinks'])
                    @foreach($order['order_list']['drinks'] as $drink)
                        <li>{{ $drink['name'] }} x {{ $drink['quantity'] }}</li>
                    @endforeach
                @endisset
            </ul>
        </div>
    </div>
@endforeach

</body>
</html>
