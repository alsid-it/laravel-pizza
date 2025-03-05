<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Меню пиццерии</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            position: relative; /* Добавлено для абсолютного позиционирования дочерних элементов */
        }

        /* Добавляем стиль для значка корзины */
        .cart-icon {
            position: absolute;
            top: 30px; /* Отступ от верхней части */
            right: 35px; /* Отступ от правой части */
            cursor: pointer;
            width: 40px; /* Ширина значка */
            height: 40px; /* Высота значка */
        }

        h1 {
            text-align: center;
            color: #e74c3c; /* Цвет заголовка */
        }

        .menu-section {
            margin: 20px 0;
        }

        .menu-section h2 {
            text-align: center;
            color: #333;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            max-width: 960px;
            margin: 0 auto; /* Центрируем сетку */
        }

        .item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .item img {
            width: 100%; /* Адаптивные изображения */
            max-width: 100px;
            border-radius: 8px;
        }

        .item h3 {
            margin: 10px 0 0 0;
            font-size: 16px;
            color: #333;
        }

        .button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #4CAF50; /* Цвет кнопки */
            color: white; /* Цвет текста кнопки */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: background 0.3s;
        }

        .button:hover {
            background-color: #45a049; /* Цвет кнопки при наведении */
        }

        /* Пример стилей для уведомления */
        .notification {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
        }

        .notification.error {
            background-color: #f44336; /* Красный цвет для ошибок */
        }

        /* Стиль для кнопки Регистрация */
        .register-button {
            position: absolute;
            top: 30px;
            left: 35px;
            padding: 10px 20px;
            background-color: #3498db; /* Синий цвет */
            color: white;
            text-decoration: none; /* Убираем подчеркивание у ссылки */
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .register-button:hover {
            background-color: #2980b9; /* Темно-синий цвет при наведении */
        }

        .header-container {
            display: flex;
            align-items: center; /* Выравнивание по вертикали */
            position: relative; /* Для абсолютного позиционирования дочерних элементов */
            margin-bottom: 20px; /* Отступ снизу для разделения с другими элементами */
        }

        .notification {
            margin-left: 170px;
            margin-top: 14px;
        }

        .notification.message {
            background-color: #95a5a6; /* Серый цвет для информационных сообщений */
            color: white; /* Цвет текста в сером уведомлении */
        }
    </style>
</head>
<body>

<div class="header-container">
    <!-- Кнопка Регистрация -->
    <a href="{{ route('register') }}" class="register-button">Регистрация</a>

    @guest()
        <a href="{{ route('auth_page') }}" class="register-button" style="margin-top: 45px;">Авторизация</a>
    @endguest

    @auth()
        <a href="{{ route('logout') }}" class="register-button" style="margin-top: 45px;">Выйти из аккаунт</a>
        <a href="{{ route('orders.my_orders') }}" class="register-button" style="margin-top: 90px;">Мои заказы</a>

        @if (auth()->user()->is_admin)
            <a href="{{ route('admin') }}" class="register-button" style="margin-top: 135px;">Админ панель</a>
        @endif
    @endauth

    <!-- Уведомления -->
    @if(session('success'))
        <div class="notification">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="notification error">
            {{ session('error') }}
        </div>
    @elseif(session('message'))
        <div class="notification message">
            {{ session('message') }}
        </div>
    @endif
</div>

<!-- Добавляем значок корзины с ссылкой на страницу корзины -->
<a href="{{ route('cart') }}">
    <img src="{{ asset('images/cart-icon.png') }}" alt="Корзина" class="cart-icon">
</a>

<h1>Пиццы</h1>

<div class="menu-section">
    <div class="grid">
        @foreach($pizzas as $pizza)
            <div class="item">
                <img src="{{ asset('storage/' . $pizza->image) }}" alt="{{ $pizza->name }}">
                <h3>{{ $pizza->name }}</h3>
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pizza_id" value="{{ $pizza->id }}">
                    <input type="hidden" name="type" value="pizza">
                    <button type="submit" class="button">Добавить в корзину</button>
                </form>
            </div>
        @endforeach
    </div>
</div>

<h1>Напитки</h1>

<div class="menu-section">
    <div class="grid">
        @foreach($drinks as $drink)
            <div class="item">
                <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}">
                <h3>{{ $drink->name }}</h3>
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="drink_id" value="{{ $drink->id }}">
                    <input type="hidden" name="type" value="drink">
                    <button type="submit" class="button">Добавить в корзину</button>
                </form>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>
