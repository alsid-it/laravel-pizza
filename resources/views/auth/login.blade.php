<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
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
            color: #e74c3c;
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
            <input type="hidden" name="auth_page" value="true">
            <input type="email" name="email" placeholder="Введите ваш email" required>
            <input type="password" name="password" placeholder="Введите ваш пароль" required>
            <button type="submit">Войти</button>
        </form>
    </div>
@endguest

</body>
