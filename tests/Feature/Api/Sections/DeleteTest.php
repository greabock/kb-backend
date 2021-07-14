<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Sections;

use App\Models\Section;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class DeleteTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'sections.destroy';
    }

    public function testUserCanDestroySection()
    {
        /** @var Section $section */
        $section = Section::factory()->create();

        $this->assertDatabaseHas('sections', ['id' => $section->id]);

        $this->callAuthorizedRouteAction([], ['section' => $section->id])
            ->assertNoContent();

        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    public function testNotFoundOnNotExistingId()
    {
        $this->callAuthorizedRouteAction([], ['section' => Uuid::uuid4()->toString()])
            ->assertNotFound();
    }
}
