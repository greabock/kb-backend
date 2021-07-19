<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Materials;

use App\Models\Enum;
use App\Models\Material;
use App\Models\Section;
use Greabock\Populator\Populator;
use Tests\Feature\Api\ActionTestCase;

class CreateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'sections.materials.create';
    }

    public function testUserCanCreateMaterialWithString(): void
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
            ->assertCreated();
    }

    public function testUserCanCreateMaterialWithEnum(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();
        $materialName = 'test material name';

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
            'name' => $materialName,
            $section->fields->first()->id => ['id' => $enum->values->first()->id],
        ], ['section' => $section->id])
            ->assertCreated()
            ->assertJsonPath('data.name', $materialName)
            ->assertJsonPath("data.{$section->fields->first()->id}.title", $enum->values->first()->title)
            ->assertJsonPath("data.{$section->fields->first()->id}.id", $enum->values->first()->id);
    }

    public function testMaterialCanLinkOtherMaterial(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();
        $materialName = 'test material name';

        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Enum', 'of' => $enum->id,]]), 'fields')
            ->create();

        $section->refresh();

        /** @var Populator $populator */
        $populator = $this->app[Populator::class];

        /** @var Material $material */
        $material = $populator->populate($section->class_name, [
            'name' => $materialName,
            $section->fields->first()->id => ['id' => $enum->values->first()->id],
        ]);

        $populator->flush();

        /** @var Section $section */
        $section2 = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Dictionary', 'of' => $section->id]]))
            ->create();
        $section2->refresh();

        $this->assertDatabaseHas('sections.' . $material->sectionId, [
            'id' => $material->id,
        ]);

        $this->callAuthorizedRouteAction([
            'name' => $materialName,
            $section2->fields->first()->id => ['id' => $material->id],
        ], ['section' => $section2->id])
            ->assertCreated();
    }


    public function testUserCanCreateMaterialWithMultipleLinks(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();
        $materialName = 'test material name';

        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => [
                'name' => 'List',
                'of' => [
                    'name' => 'Enum',
                    'of' => $enum->id,
                ]
            ]]), 'fields')
            ->create();

        $section->refresh();

        $this->callAuthorizedRouteAction([
            'name' => $materialName,
            $section->fields->first()->id => [['id' => $enum->values->first()->id], ['id' => $enum->values->first()->id]],
        ], ['section' => $section->id])
            ->dump()
            ->assertCreated();
    }

    public function testRequiredFields(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(), 'fields'
        )->create();

        $section->refresh();
        $this->callAuthorizedRouteAction([], ['section' => $section->id])
            ->assertJsonValidationErrors([$section->fields->first()->id, 'name']);
    }

    public function testNotRequiredFields(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['required' => false]), 'fields'
        )->create();

        $section->refresh();
        $this->callAuthorizedRouteAction([], ['section' => $section->id])
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors([$section->fields->first()->id]);
    }
}
