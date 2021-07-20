<?php

declare(strict_types=1);

namespace Database\Factories\Section;

use App\Models\Section\Field;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;


class FieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Field::class;

    public function definition()
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'title' => $this->faker->word,
            'description' => $this->faker->title,
            'sort_index' => 0,
            'type' => ['name' => 'String'],
            'required' => true,
            'is_present_in_card' => true,
            'is_filterable' => false,
        ];
    }
}
