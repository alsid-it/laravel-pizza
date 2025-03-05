<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ Панель - Пиццерия</title>
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
            color: #34495e;
            margin-top: 50px;
        }

        .admin-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .admin-actions button {
            padding: 15px 30px;
            font-size: 18px;
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 8px;
            margin: 10px 0;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .admin-actions button:hover {
            background-color: #1a5984;
            transform: scale(1.05);
        }

        .admin-actions button:active {
            transform: scale(1.02);
        }

        .admin-actions button:first-child {
            background-color: #27ae60;
        }

        .admin-actions button:first-child:hover {
            background-color: #218c4a;
        }

        .admin-actions button:first-child:active {
            transform: scale(1.02);
        }

        .admin-actions button:last-child {
            background-color: #e74c3c;
        }

        .admin-actions button:last-child:hover {
            background-color: #c0392b;
        }

        .admin-actions button:last-child:active {
            transform: scale(1.02);
        }
    </style>
</head>
<body>

<a href="{{ route('showcase') }}">
    <img src="{{ asset('images/pizza.png') }}" alt="Домик" class="pizza-icon">
</a>

<h1>Админ Панель</h1>

<div class="admin-actions">
    <a href="{{ route('admin.add-pizza') }}">
        <button type="submit">Добавить пиццу</button>
    </a>

    <a href="{{ route('admin.add-drink') }}">
        <button type="submit">Добавить напиток</button>
    </a>

    <a href="{{ route('admin.change-product') }}">
        <button type="submit">Изменить товары</button>
    </a>

    <a href="{{ route('admin.delete-product') }}">
        <button type="submit">Удалить товары</button>
    </a>

    <a href="{{ route('admin.order-statuses') }}">
        <button type="submit">Статусы заказов</button>
    </a>

</div>

</body>
</html>
