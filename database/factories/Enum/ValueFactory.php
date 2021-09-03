<?php

declare(strict_types=1);

namespace Database\Factories\Enum;

use App\Models\Enum\Value;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Value::class;


    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'title' => $this->faker->word,
        ];
    }
}
