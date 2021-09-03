<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Sections;

use App\Models\Section;
use Tests\Feature\Api\ActionTestCase;

class MassUpdateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'sections.massUpdate';
    }

    public function testAdminCanSortSections(): void
    {

        $section1 = Section::factory()->create(['sort_index' => 0]);
        $section2 = Section::factory()->create(['sort_index' => 1]);

        $this->callAuthorizedByAdminRouteAction([
            [
                'id' => $section1->id,
                'sort_index' => 1,
            ],
            [
                'id' => $section2->id,
                'sort_index' => 0,
            ]
        ])->assertOk();

        $this->assertDatabaseHas('sections', [
            'id' => $section1->id,
            'sort_index' => 1,
        ]);

        $this->assertDatabaseHas('sections', [
            'id' => $section2->id,
            'sort_index' => 0,
        ]);

    }
}
