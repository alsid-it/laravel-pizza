<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            return redirect()->intended(route('showcase'))->with('message', 'Вы успешно вышли из системы');
        }

        return redirect()->intended(route('showcase'))->with('message', 'Вы не были авторизованы.');
    }

    public function auth(Request $request) {
        // Валидация данных формы
        $validated = $request->validate([
            'email' => 'required|email', // Поле email обязательно и должно быть корректным адресом
            'password' => 'required',   // Поле password обязательно
        ]);

        $authPage = $request->input('auth_page'); // Будет null, если поле не было отправлено

        // Попытка аутентификации пользователя
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $request->has('remember'))) {
            // Аутентификация прошла успешно
            $request->session()->regenerate(); // Обновляем сессию для предотвращения фиксации сеанса (session fixation)

            if ($authPage) {
                return redirect()->intended(route('showcase'))->with('success', 'Вы успешно вошли в систему!');
            }

            // Перенаправляем пользователя на главную страницу (или другую)
            return redirect()->intended(route('cart'))->with('success', 'Вы успешно вошли в систему!');
        }

        // Неудачная аутентификация. Возвращаем обратно с сообщением об ошибке.
        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->withInput($request->only('email')); // Чтобы email оставался в поле после отправки
    }
}
