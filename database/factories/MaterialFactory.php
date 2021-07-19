<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Material;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class MaterialFactory extends Factory
{
    protected $model;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);
    }

    public function setModel(string $model)
    {
        assert(is_subclass_of($model, Material::class));
        $this->model = $model;

        return $this;
    }

    public function definition()
    {
        dd('gettModel');
        return Section::findOrFail($this->model::$sectionId)->fields->keyBy('id')->map(function (Section\Field $field) {
            return match ($field->type['name']) {
                'String' => $this->faker->word,
                'Text', 'Wiki' => $this->faker->text,
                'Integer' => $this->faker->numberBetween($field->type['min'], $field->type['max']),
                'Float' => $this->faker->randomFloat(null, $field->type['min'], $field->type['max']),
                'Boolean' => $this->faker->boolean,
                default => null,
            };
        })->filter()->toArray();
    }
}
