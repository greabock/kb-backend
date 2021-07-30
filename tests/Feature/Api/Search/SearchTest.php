<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Search;

use App\Jobs\CreateMaterialDocument;
use App\Jobs\CreateSectionIndex;
use App\Jobs\UpdateMaterialClass;
use App\Models\Material;
use App\Models\Section;
use App\Services\TableBuilder;
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
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Text']]), 'fields')
            ->create();

        $section->refresh();

        $data = ['name' => 'Name'];

        foreach ($section->fields as $field) {
            $data[$field->id] = <<<TEXT
            Laravel невероятно масштабируем.
            Благодаря удобному для масштабирования характеру PHP и встроенной
            поддержке быстрых распределенных систем кеширования, таких как Redis,
            горизонтальное масштабирование с Laravel очень просто. Фактически, приложения
            Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.
            Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать
             приложение Laravel в практически неограниченном масштабе
            с использованием новейшей бессервернойтехнологии AWS.
            TEXT;
        }

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'удобный'])
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name']]])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $material->id);
    }
}
