<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить Напиток</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            position: relative;
        }

        h1 {
            text-align: center;
            color: #3498db;
        }

        .form-container {
            margin: 20px auto;
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #333;
            text-align: center;
        }

        .form-container label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }

        .form-container input {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .error-messages {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .error-messages ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .error-messages li {
            font-size: 14px;
        }

        .notification {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
            margin-left: 170px;
            margin-top: 14px;
        }
    </style>
</head>
<body>
<h1>Добавить Напиток</h1>

<!-- Уведомления -->
@if(session('success'))
    <div class="notification">
        {{ session('success') }}
    </div>
@endif

<div class="form-container">
    @if ($errors->any())
        <div class="error-messages">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.drinks.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">Название Напитка:</label>
            <input type="text" name="name" id="name" placeholder="Введите название напитка" value="{{ old('name') }}" required>
        </div>
        <div>
            <label for="image">Изображение Напитка:</label>
            <input type="file" name="image" id="image" accept="image/*" required>
        </div>
        <div>
            <button type="submit">Добавить Напиток</button>
        </div>
    </form>
</div>
</body>
</html>
