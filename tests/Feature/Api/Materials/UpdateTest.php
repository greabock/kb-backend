<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Materials;

use App\Models\Material;
use App\Models\Section;
use Tests\Feature\Api\ActionTestCase;

class UpdateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'sections.materials.update';
    }

    public function testUserCanUpdateMaterial(): void
    {
        $oldName = 'Old Name';
        $newName = 'New Name';

        /** @var Section $section */
        $section = Section::factory()->create();

        /** @var Material $material */
        $material = $this->populator()->populate(
            $section->class_name,
            ['name' => $oldName],
        );

        $this->populator()->flush();

        $this->callAuthorizedRouteAction([
            'name' => $newName
        ], ['section' => $section->id, 'material' => $material->id])
            ->assertOk()
            ->assertJsonPath('data.name', $newName);

        $this->assertDatabaseHas($section->tableName, [
            'id' => $material->id,
            'name' => $newName,
        ]);
    }

    public function testFieldsAreNoteRequired(): void
    {
        /** @var Section $section */
        $section = Section::factory()->create();

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, ['name' => 'Name']);

        $this->populator()->flush();

        $this->callAuthorizedRouteAction([], ['section' => $section->id, 'material' => $material->id])->assertOk();
    }
}
