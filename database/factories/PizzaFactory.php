<?php

namespace Database\Factories;

use App\Models\Pizza;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pizza>
 */
class PizzaFactory extends Factory
{
    protected const PIZZA_NAMES = [
        'Маргарита',
        'Пепперони',
        'Гавайская',
        'Вегетарианская',
        'Четыре сыра',
        'Мясная',
        'С морепродуктами',
        'Грибная',
        'Барбекю',
        'Охотничья',
        'Деревенская',
        'Карбонара',
        'Мексиканская',
        'С тунцом',
        'Сырная',
    ];

    protected array $usedPizzaNames = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->getRandomPizzaName(),
        ];
    }

    protected function getRandomPizzaName(): string
    {
        if (count(self::PIZZA_NAMES) === count($this->usedPizzaNames)) {
            $this->usedPizzaNames = [];
        }

        $availableNames = array_diff(self::PIZZA_NAMES, $this->usedPizzaNames);
        $randomName = fake()->randomElement($availableNames);
        $this->usedPizzaNames[] = $randomName;

        return $randomName;
    }
}
