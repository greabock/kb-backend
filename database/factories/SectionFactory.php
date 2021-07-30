<?php

namespace Database\Factories;

use App\Jobs\CreateSectionIndex;
use App\Jobs\UpdateMaterialClass;
use App\Models\Section;
use App\Services\TableBuilder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class SectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Section::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'title' => 'test',
            'image' => null,
            'is_dictionary' => true,
            'is_navigation' => true,
            'sort_index' => 1,
            'indexing' => true,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Section $section) {
            app(TableBuilder::class)->create($section);
            app()->call([(new CreateSectionIndex($section->id)), 'handle']);
            app()->call([(new UpdateMaterialClass($section->id)), 'handle']);
        });
    }
}
