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

        $section = Section::factory()->has(Section\Field::factory(), 'fields')
            ->create(['title' => 'old_title']);

        $this->callAuthorizedByAdminRouteAction(['title' => $newTitle], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.id', $section->id)
            ->assertJsonPath('data.title', $newTitle);
    }

    public function testUserCanRemoveSectionFields(): void
    {
        $newTitle = 'new_title';

        /** @var Section $section */
        $section = Section::factory()->has(Section\Field::factory()->count(2), 'fields')
            ->create(['title' => 'old_title']);

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'title' => $newTitle,
            'fields' => [$section->fields->first()->toArray()],
        ], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.id', $section->id)
            ->assertJsonPath('data.title', $newTitle);
    }

    public function testUserCanAddSectionFields(): void
    {
        $newTitle = 'new_title';

        /** @var Section $section */
        $section = Section::factory()->has(Section\Field::factory()->count(2), 'fields')
            ->create(['title' => 'old_title']);

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'title' => $newTitle,
            'fields' => [
                [
                    'title' => 'New Field',
                    'description' => 'New Field Description',
                    'sort_index' => -1,
                    'type' => ['name' => 'String'],
                    'required' => true,
                    'is_present_in_card' => true,
                    'filter_sort_index' => null,
                ],
                ...$section->fields->toArray()
            ],
        ], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.id', $section->id)
            ->assertJsonPath('data.title', $newTitle)
            ->assertJsonPath('data.fields.0.title', 'New Field');
    }

    public function testNotFoundOnNotExistingId()
    {
        $this->callAuthorizedRouteAction(['title' => 'test'], ['section' => Uuid::uuid4()->toString()])
            ->assertNotFound();
    }

}
