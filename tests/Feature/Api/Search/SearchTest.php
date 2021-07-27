<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Search;

use App\Models\Material;
use App\Models\Section;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Api\ActionTestCase;

class SearchTest extends ActionTestCase
{
    use WithFaker;

    public function getRouteName(): string
    {
        return 'search';
    }

    public function testUserCanSearch()
    {
        /** @var Section $section */
        $section = Section::factory()->has(Section\Field::factory(['type' => ['name' => 'Text']]), 'fields')->create();
        $section->refresh();

        $data = ['name' => 'Name'];

        foreach ($section->fields as $field) {
            $data[$field->id] = 'Привет! Как дела?';
        }

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, $data);

        $this->populator()->flush();

        usleep(1000000);

        $this->callAuthorizedRouteAction(['search' => 'дела'])
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name']]])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $material->id);
    }
}
