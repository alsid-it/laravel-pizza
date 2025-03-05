<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePizzaRequest;
use App\Http\Requests\UpdatePizzaRequest;
use App\Http\Resources\Api\V1\PizzaResource;
use App\Models\Pizza;

class PizzaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PizzaResource::collection(Pizza::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePizzaRequest $request)
    {
        $imagePath = null;

        // Проверяем, было ли передано изображение
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        // Создаем пиццу
        $createdPizza = Pizza::create([
            'name' => $request->name,
            'image' => $imagePath,
        ]);

        // Формируем ответ с дополнительной метаинформацией
        return response()->json([
            'message' => 'Pizza created successfully.', // Сообщение об успехе
            'data' => new PizzaResource($createdPizza), // Данные созданной пиццы
            'links' => [
                'self' => url("/api/v1/pizzas/{$createdPizza->id}"), // Ссылка на созданный ресурс
                'list' => url('/api/v1/pizzas'), // Ссылка на список всех пицц
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pizza $pizza)
    {
        return response()->json([
            'message' => 'Pizza found.',
            'data' => new PizzaResource($pizza),
            'links' => [
                'self' => url("/api/v1/pizzas/{$pizza->id}"),
                'list' => url('/api/v1/pizzas'),
            ],
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePizzaRequest $request, Pizza $pizza)
    {
        $pizza->update($request->all());

        return response()->json([
            'message' => 'Pizza updated successfully.',
            'data' => new PizzaResource($pizza),
            'links' => [
                'self' => url("/api/v1/pizzas/{$pizza->id}"),
                'list' => url('/api/v1/pizzas'),
            ],
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pizza $pizza)
    {
        $pizza->delete();

        return response()->noContent();
    }
}
