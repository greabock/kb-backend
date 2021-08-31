<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Materials;

use App\Jobs\CreateMaterialDocument;
use App\Models\Material;
use App\Models\Section;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Api\ActionTestCase;

class IndexTest extends ActionTestCase
{
    use WithFaker;

    public function getRouteName(): string
    {
        return 'sections.materials.index';
    }

    public function testUserCanGetPaginatedResult(): void
    {
        /** @var Section $section */
        $section = Section::factory()->create();

        $section->refresh();

        /** @var Material $material */
        $material = $this->populator()->populate(
            $section->class_name,
            array_merge(['name' => 'Name'])
        );
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);


        /** @var Material $material */
        $material = $this->populator()->populate(
            $section->class_name,
            array_merge(['name' => 'zzz'])
        );
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'ame'], ['section' => $section->id])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }



    public function testUserCanGetPaginatedResultWithEmptySearch(): void
    {
        /** @var Section $section */
        $section = Section::factory()->create();

        $section->refresh();

        /** @var Material $material */
        $material = $this->populator()->populate(
            $section->class_name,
            array_merge(['name' => 'Name'])
        );
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        /** @var Material $material */
        $material = $this->populator()->populate(
            $section->class_name,
            array_merge(['name' => 'zzz'])
        );
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction([], ['section' => $section->id])
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }
}
