<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Materials;

use App\Models\Material;
use App\Models\Section;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Api\ActionTestCase;

class ShowTest extends ActionTestCase
{
    use WithFaker;

    public function getRouteName(): string
    {
        return 'sections.materials.show';
    }

    public function testUserCanSeeMaterialDetails(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory()->count(3),
            'fields',
        )->create();

        $section->refresh();

        $fieldValues = $section->fields->keyBy('id')->map(fn() => $this->faker->word)
            ->toArray();

        /** @var Material $material */
        $material = $this->populator()->populate(
            $section->class_name,
            array_merge(['name' => 'Name'], $fieldValues)
        );

        $this->populator()->flush();

        $this->callAuthorizedRouteAction([], ['material' => $material->id, 'section' => $section->id])
            ->assertJsonStructure(['data' => array_keys($fieldValues)])
            ->assertOk();
    }
}
