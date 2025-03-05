<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - Пиццерия</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            position: relative;
        }

        .pizza-icon {
            position: absolute;
            top: 30px;
            right: 43px;
            cursor: pointer;
            width: 40px;
            height: 40px;
        }

        h1 {
            text-align: center;
            color: #e74c3c;
        }

        .cart-items {
            margin: 20px auto;
            max-width: 960px;
        }

        .item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .item img {
            width: 100px;
            height: auto;
            border-radius: 8px;
            margin-right: 15px;
        }

        .item h3 {
            flex-grow: 1;
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .remove-button, .update-button {
            padding: 5px 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            margin-left: 10px;
        }

        .remove-button:hover, .update-button:hover {
            background-color: #c0392b;
        }

        .quantity-input {
            width: 50px;
            margin-left: 10px;
        }

        .form-container {
            margin: 20px auto;
            max-width: 960px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #333;
            text-align: center;
        }

        .form-container input, .form-container button {
            display: block;
            width: 98%;
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container button {
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .form-container button:hover {
            background-color: #c0392b;
        }

        .notification {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }

        .notification.error {
            background-color: #f44336;
        }

        .error {
            color: #ffffff; /* Цвет текста ошибок */
            font-size: 16px; /* Меньший размер шрифта для ошибок */
            margin-top: 5px; /* Отступ сверху */
        }
    </style>
</head>
<body>

<a href="{{ route('showcase') }}">
    <img src="{{ asset('images/pizza.png') }}" alt="Домик" class="pizza-icon">
</a>

<h1>Корзина</h1>

<div class="cart-items">
    @isset($cart['pizzas'])
        @foreach($cart['pizzas'] as $pizzaId => $pizza)
            <div class="item">
                <img src="{{ $pizza['image'] }}" alt="{{ $pizza['name'] }}">
                <h3>{{ $pizza['name'] }}</h3>
                <form action="{{ route('cart.update') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="pizza_id" value="{{ $pizzaId }}">
                    <input type="hidden" name="type" value="pizza">
                    <input type="number" name="quantity" value="{{ $pizza['quantity'] }}" class="quantity-input" min="1">
                    <button type="submit" class="update-button">Изменить</button>
                </form>
                <form action="{{ route('cart.delete') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="pizza_id" value="{{ $pizzaId }}">
                    <input type="hidden" name="type" value="pizza">
                    <button type="submit" class="remove-button">Удалить</button>
                </form>
            </div>
        @endforeach
    @endisset
    @isset($cart['drinks'])
        @foreach($cart['drinks'] as $drinkId => $drink)
            <div class="item">
                <img src="{{ $drink['image'] }}" alt="{{ $drink['name'] }}">
                <h3>{{ $drink['name'] }}</h3>
                <form action="{{ route('cart.update') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="drink_id" value="{{ $drinkId }}">
                    <input type="hidden" name="type" value="drink">
                    <input type="number" name="quantity" value="{{ $drink['quantity'] }}" class="quantity-input" min="1">
                    <button type="submit" class="update-button">Изменить</button>
                </form>
                <form action="{{ route('cart.delete') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="drink_id" value="{{ $drinkId }}">
                    <input type="hidden" name="type" value="drink">
                    <button type="submit" class="remove-button">Удалить</button>
                </form>
            </div>
        @endforeach
    @endisset
</div>

@if(
    (isset($cart['drinks'])
    || isset($cart['$pizzas']))
    && (
        count($cart['drinks']) > 0
        || count($cart['pizzas']) > 0
    )
)
    <!-- Проверяем, авторизован ли пользователь -->
    @guest
        <div class="form-container">
            <h2>Вход в учетную запись</h2>
            @if ($errors->any())
                <div class="notification error">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            <form action="{{ route('auth') }}" method="POST">
                @csrf
                <input type="email" name="email" placeholder="Введите ваш email" required>
                <input type="password" name="password" placeholder="Введите ваш пароль" required>
                <button type="submit">Войти</button>
            </form>
        </div>
    @else
        <div class="form-container">
            <h2>Данные для доставки</h2>
            <form action="{{ route('cart.order') }}" method="POST">
                @csrf

                @if ($errors->has('phone'))
                    <div class="error">{{ $errors->first('phone') }}</div>
                @endif
                <input type="text" name="phone" id="phone" placeholder="+375 (XX) XXX-XX-XX" value="{{ old('phone') }}" required>

                @if ($errors->has('email'))
                    <div class="error">{{ $errors->first('email') }}</div>
                @endif
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" placeholder="Введите ваш email" required>

                @if ($errors->has('address'))
                    <div class="error">{{ $errors->first('address') }}</div>
                @endif
                <input type="text" name="address" placeholder="Введите ваш адрес" value="{{ old('address') }}" required>

                @if ($errors->has('delivery_time'))
                    <div class="error">{{ $errors->first('delivery_time') }}</div>
                @endif
                <input type="time" name="delivery_time" value="{{ old('delivery_time') }}" required>

                <button type="submit">Оформить заказ</button>
            </form>

        </div>
    @endguest
@endif

</body>
</html>
