<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enum;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;


class EnumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Enum::class;


    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'title' => $this->faker->word,
        ];
    }

    public function withValues()
    {
        return $this->state(function () {
            return [
                'account_status' => 'suspended',
            ];
        });
    }
}
