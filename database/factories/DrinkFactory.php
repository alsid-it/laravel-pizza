<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Drink>
 */
class DrinkFactory extends Factory
{
    protected array $drinkNames = [
        'Кола',
        'Пепси',
        'Фанта',
        'Спрайт',
        'Лимонад',
        'Апельсиновый сок',
        'Яблочный сок',
        'Чай со льдом',
        'Кофе',
        'Молочный коктейль',
        'Смузи',
        'Минеральная вода',
        'Энергетик',
        'Пиво',
        'Вино',
    ];

    protected array $usedDrinkNames = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->getRandomDrinkName()
        ];
    }

    protected function getRandomDrinkName(): string
    {
        if (count($this->drinkNames) === count($this->usedDrinkNames)) {
            $this->usedDrinkNames = [];
        }

        $availableNames = array_diff($this->drinkNames, $this->usedDrinkNames);
        $randomName = fake()->randomElement($availableNames);
        $this->usedDrinkNames[] = $randomName;

        return $randomName;
    }
}
