<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация пользователя</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* Стили для формы регистрации */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #e74c3c;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 94%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
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
            color: #f44336; /* Цвет текста ошибок */
            font-size: 0.875em; /* Меньший размер шрифта для ошибок */
            margin-top: 5px; /* Отступ сверху */
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Регистрация</h1>

    @if(session('success'))
        <div class="notification">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="notification error">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Имя</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @if ($errors->has('name'))
                <div class="error">{{ $errors->first('name') }}</div>
            @endif
        </div>
        <div class="form-group">
            <label for="email">Электронная почта</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @if ($errors->has('email'))
                <div class="error">{{ $errors->first('email') }}</div>
            @endif
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
            @if ($errors->has('password'))
                <div class="error">{{ $errors->first('password') }}</div>
            @endif
        </div>
        <div class="form-group">
            <label for="password_confirmation">Подтверждение пароля</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            @if ($errors->has('password_confirmation'))
                <div class="error">{{ $errors->first('password_confirmation') }}</div>
            @endif
        </div>
        <button type="submit" class="button">Зарегистрироваться</button>
    </form>
</div>

</body>
</html>
