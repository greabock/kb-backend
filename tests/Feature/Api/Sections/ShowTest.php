<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Sections;

use App\Models\Section;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class ShowTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'sections.show';
    }

    public function testUserCanSeeSection()
    {
        /** @var Section $section */
        $section = Section::factory()->has(Section\Field::factory())->create();

        $this->callAuthorizedRouteAction([],['section' => $section->id])
            ->assertOk()
            ->assertJsonStructure(['data' => [
                'title',
                'image',
                'is_dictionary',
                'is_navigation',
                'sort_index',
                'fields' => [[
                    'title',
                    'description',
                    'sort_index',
                    'type',
                    'required',
                    'is_present_in_card',
                ]]
            ]]);
    }

    public function testNotFoundOnNotExistingId()
    {
        $this->callAuthorizedRouteAction([],['section' => Uuid::uuid4()->toString()])
            ->assertNotFound();
    }
}
