<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Sections;

use App\Models\Section;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class UpdateTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'sections.update';
    }

    public function testUserCanUpdateSection(): void
    {


        $newTitle = 'new_title';
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory()
        )->create(['title' => 'old_title']);

        $this->callAuthorizedByAdminRouteAction(['title' => $newTitle], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.id', $section->id)
            ->assertJsonPath('data.title', $newTitle);
    }

    public function testNotFoundOnNotExistingId()
    {
        $this->callAuthorizedRouteAction(['title' => 'test'], ['section' => Uuid::uuid4()->toString()])
            ->assertNotFound();
    }

}
