<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Sections;

use App\Models\Section;
use Tests\Feature\Api\ActionTestCase;

class IndexTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'sections.index';
    }


    public function testUserCanSeeSections(): void
    {
        $section1 = Section::factory()->create(['sort_index' => 1]);
        $section2 = Section::factory()->create(['sort_index' => 0]);

        $this
            ->callAuthorizedRouteAction()
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [
                ['id', 'title', 'image', 'is_dictionary', 'is_navigation', 'sort_index']
            ]])
            ->assertJsonPath('data.0.id', $section2->id)
            ->assertJsonPath('data.1.id', $section1->id);
    }
}
