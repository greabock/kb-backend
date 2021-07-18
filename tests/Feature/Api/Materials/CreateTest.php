<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Materials;

use App\Models\Enum;
use App\Models\Section;
use Tests\Feature\Api\ActionTestCase;

class CreateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'sections.materials.create';
    }

    public function testUserCanCreateMaterial()
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedRouteAction([
            'name' => 'test name',
            $section->fields->first()->id => 'Привет!',
        ], ['section' => $section->id])
            ->assertCreated()
            ->dump();
    }

    public function testUserCanCreateWithEnum()
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();

        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory([
                'type' => [
                    'name' => 'Enum',
                    'of' => $enum->id,
                ]
            ]), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedRouteAction([
            'name' => 'test name',
            $section->fields->first()->id => ['id' => $enum->values->first()->id],
        ], ['section' => $section->id])
            ->assertCreated();
    }
}
