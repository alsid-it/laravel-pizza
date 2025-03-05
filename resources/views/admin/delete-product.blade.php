<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление продуктов - Пиццерия</title>
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

        .product-list {
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

        .remove-button {
            padding: 5px 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            margin-left: 10px;
        }

        .remove-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<a href="{{ route('showcase') }}">
    <img src="{{ asset('images/pizza.png') }}" alt="Домик" class="pizza-icon">
</a>

<h1>Удаление продуктов</h1>

<div class="product-list">
    <h2>Пиццы</h2>
    @isset($pizzas)
        @foreach($pizzas as $pizza)
            <div class="item">
                <img src="{{ asset('storage/' . $pizza->image) }}" alt="{{ $pizza->name }}">
                <h3>{{ $pizza->name }}</h3>
                <form action="{{ route('admin.delete-product', ['type' => 'pizza', 'id' => $pizza->id]) }}"
                      method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="remove-button">Удалить</button>
                </form>
            </div>
        @endforeach
    @else
        <p>Пиццы отсутствуют.</p>
    @endisset

    <h2>Напитки</h2>
    @isset($drinks)
        @foreach($drinks as $drink)
            <div class="item">
                <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}">
                <h3>{{ $drink->name }}</h3>
                <form action="{{ route('admin.delete-product', ['type' => 'drink', 'id' => $drink->id]) }}"
                      method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="remove-button">Удалить</button>
                </form>
            </div>
        @endforeach
    @else
        <p>Напитки отсутствуют.</p>
    @endisset
</div>

</body>
</html>
