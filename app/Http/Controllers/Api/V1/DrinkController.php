<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrinkRequest;
use App\Http\Requests\UpdateDrinkRequest;
use App\Http\Resources\Api\V1\DrinkResource;
use App\Models\Drink;

class DrinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DrinkResource::collection(Drink::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDrinkRequest $request)
    {
        $imagePath = null;

        // Проверяем, было ли передано изображение
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        // Создаем пиццу
        $createdDrink = Drink::create([
            'name' => $request->name,
            'image' => $imagePath,
        ]);

        // Формируем ответ с дополнительной метаинформацией
        return response()->json([
            'message' => 'Drink created successfully.', // Сообщение об успехе
            'data' => new DrinkResource($createdDrink), // Данные созданного напитка
            'links' => [
                'self' => url("/api/v1/orders/{$createdDrink->id}"), // Ссылка на созданный ресурс
                'list' => url('/api/v1/orders'), // Ссылка на список всех напитков
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Drink $drink)
    {
        return response()->json([
            'message' => 'Drink found.',
            'data' => new DrinkResource($drink),
            'links' => [
                'self' => url("/api/v1/orders/{$drink->id}"),
                'list' => url('/api/v1/orders'),
            ],
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDrinkRequest $request, Drink $drink)
    {
        $drink->update($request->all());

        return response()->json([
            'message' => 'Drink updated successfully.',
            'data' => new DrinkResource($drink),
            'links' => [
                'self' => url("/api/v1/orders/{$drink->id}"),
                'list' => url('/api/v1/orders'),
            ],
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Drink $drink)
    {
        $drink->delete();

        return response()->noContent();
    }
}
