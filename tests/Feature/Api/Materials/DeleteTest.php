<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Materials;

use App\Models\Material;
use App\Models\Section;
use Tests\Feature\Api\ActionTestCase;

class DeleteTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'sections.materials.destroy';
    }

    public function testUserCanDeleteMaterial(): void
    {
        /** @var Section $section */
        $section = Section::factory()->create();

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, ['name' => 'Name']);
        $this->populator()->flush();

        $this->callAuthorizedRouteAction([], ['section' => $section->id, 'material' => $material->id])
            ->assertNoContent();

        $this->assertDatabaseMissing($section->tableName, ['id' => $material->id, 'deleted_at' => null]);
    }
}
