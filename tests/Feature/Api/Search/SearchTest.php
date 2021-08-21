<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Search;

use App\Jobs\CreateMaterialDocument;
use App\Models\File;
use App\Models\Material;
use App\Models\Section;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class SearchTest extends ActionTestCase
{
    use WithFaker;

    public function getRouteName(): string
    {
        return 'search';
    }

    public function testEmptySearch()
    {
        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Text']]), 'fields')
            ->create();

        $section->refresh();

        $data = ['name' => 'Об особенностях Laravel'];

        foreach ($section->fields as $field) {
            $data[$field->id] = <<<TEXT
            Laravel невероятно масштабируем.
            Благодаря удобному для масштабирования характеру PHP и встроенной
            поддержке быстрых распределенных систем кеширования, таких как Redis,
            горизонтальное масштабирование с Laravel очень просто. Фактически, приложения
            Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.
            Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать
             приложение Laravel в практически неограниченном масштабе
            с использованием новейшей бессерверной технологии AWS.
            TEXT;
        }

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => '', 'materials' =>  true])
            ->assertOk()
            ->dump()
            ->assertJsonPath('data.materials.0.section.id', $section->id)
            ->assertJsonPath('data.materials.0.material.id', $material->id)
            ->assertJsonPath('data.materials.0.material.name', $material->name)
            ->assertJsonPath('data.materials.1', null)
            ->assertJsonPath('data.files', []);
    }

    public function testUserCanSearchInMaterial()
    {
        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Text']]), 'fields')
            ->create();

        $section->refresh();

        $data = ['name' => 'Об особенностях Laravel'];

        foreach ($section->fields as $field) {
            $data[$field->id] = <<<TEXT
            Laravel невероятно масштабируем.
            Благодаря удобному для масштабирования характеру PHP и встроенной
            поддержке быстрых распределенных систем кеширования, таких как Redis,
            горизонтальное масштабирование с Laravel очень просто. Фактически, приложения
            Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.
            Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать
             приложение Laravel в практически неограниченном масштабе
            с использованием новейшей бессерверной технологии AWS.
            TEXT;
        }

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'удобный', 'materials' => true])
            ->assertOk()
            ->assertJsonPath('data.materials.0.section.id', $section->id)
            ->assertJsonPath('data.materials.0.material.id', $material->id)
            ->assertJsonPath('data.materials.0.material.name', $material->name)
            ->assertJsonPath('data.materials.1', null)
            ->assertJsonPath('data.files', []);
    }

    public function testUserCanSearchWithOneFile()
    {
        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['title' => 'My file', 'type' => ['name' => 'File']]), 'fields')
            ->create();

        $section->refresh();


        File::create([
            'id' => $fileId = Uuid::uuid4()->toString(),
            'realpath' => '_',
            'url' => route('files.download', [$fileId]),
            'indexed' => true,
            'content' => <<<TEXT
            Laravel невероятно масштабируем.
            Благодаря удобному для масштабирования характеру PHP и встроенной
            поддержке быстрых распределенных систем кеширования, таких как Redis,
            горизонтальное масштабирование с Laravel очень просто. Фактически, приложения
            Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.
            Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать
             приложение Laravel в практически неограниченном масштабе
            с использованием новейшей бессерверной технологии AWS.
            TEXT,
            'name' => 'Трактат о Laravel',
            'extension' => 'docx',
        ]);


        $data = [
            'name' => 'Об особенностях Laravel',
            $section->fields->first()->id => ['id' => $fileId],
        ];

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'удобный'])
            ->assertOk()
            ->assertJsonPath('data.files.0.section.id', $section->id)
            ->assertJsonPath('data.files.0.material.id', $material->id)
            ->assertJsonPath('data.files.0.material.name', $material->name)
            ->assertJsonPath('data.files.1', null)
            ->assertJsonPath('data.materials', []);
    }


    public function testUserCanSearchWithMultipleFiles()
    {
        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory([
                'title' => 'My file', 'type' => [
                    'name' => 'List',
                    'of' => [
                        'name' => 'File'
                    ]
                ]
            ]), 'fields')
            ->create();

        $section->refresh();

        File::create([
            'id' => $fileId = Uuid::uuid4()->toString(),
            'realpath' => '_',
            'url' => route('files.download', [$fileId]),
            'indexed' => true,
            'content' => <<<TEXT
            Laravel невероятно масштабируем.
            Благодаря удобному для масштабирования характеру PHP и встроенной
            поддержке быстрых распределенных систем кеширования, таких как Redis,
            горизонтальное масштабирование с Laravel очень просто. Фактически, приложения
            Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.
            Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать
             приложение Laravel в практически неограниченном масштабе
            с использованием новейшей бессерверной технологии AWS.
            TEXT,
            'name' => 'Трактат о Laravel',
            'extension' => 'doc',
        ]);


        File::create([
            'id' => $fileId2 = Uuid::uuid4()->toString(),
            'realpath' => '_',
            'url' => route('files.download', [$fileId2]),
            'indexed' => true,
            'content' => <<<TEXT
            Laravel невероятно масштабируем. Трактат
            Благодаря удобному для масштабирования характеру PHP и встроенной
            поддержке быстрых распределенных систем кеширования, таких как Redis,
            горизонтальное масштабирование с Laravel очень просто. Фактически, приложения
            Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.
            Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать
             приложение Laravel в практически неограниченном масштабе
            с использованием новейшей бессерверной технологии AWS.
            TEXT,
            'name' => 'Об особенностях Laravel',
            'extension' => 'docx',
        ]);

        $data = [
            'name' => 'zzz',
            $section->fields->first()->id => [['id' => $fileId], ['id' => $fileId2]],
        ];

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'трактат'])
            ->assertOk();
    }

    public function testUserCanSearchWithFilterExtensions(): void
    {
        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory([
                'title' => 'My file', 'type' => [
                    'name' => 'List',
                    'of' => [
                        'name' => 'File'
                    ]
                ]
            ]), 'fields')
            ->create();

        $section->refresh();


        File::create([
            'id' => $fileId = Uuid::uuid4()->toString(),
            'realpath' => '_',
            'url' => route('files.download', [$fileId]),
            'indexed' => true,
            'content' => <<<TEXT
            Laravel невероятно масштабируем.
            Благодаря удобному для масштабирования характеру PHP и встроенной
            поддержке быстрых распределенных систем кеширования, таких как Redis,
            горизонтальное масштабирование с Laravel очень просто. Фактически, приложения
            Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.
            Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать
             приложение Laravel в практически неограниченном масштабе
            с использованием новейшей бессерверной технологии AWS.
            TEXT,
            'name' => 'Трактат о Laravel',
            'extension' => 'doc',
        ]);


        File::create([
            'id' => $fileId2 = Uuid::uuid4()->toString(),
            'realpath' => '_',
            'url' => route('files.download', [$fileId2]),
            'indexed' => true,
            'content' => <<<TEXT
            Laravel невероятно масштабируем. Трактат
            Благодаря удобному для масштабирования характеру PHP и встроенной
            поддержке быстрых распределенных систем кеширования, таких как Redis,
            горизонтальное масштабирование с Laravel очень просто. Фактически, приложения
            Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.
            Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать
             приложение Laravel в практически неограниченном масштабе
            с использованием новейшей бессерверной технологии AWS.
            TEXT,
            'name' => 'Об особенностях Laravel',
            'extension' => 'docx',
        ]);

        $data = [
            'name' => 'zzz',
            $section->fields->first()->id => [['id' => $fileId], ['id' => $fileId2]],
        ];

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'трактат', 'extensions' => ['doc']])
            ->assertOk()
            ->assertJsonPath('data.files.0.section.id', $section->id)
            ->assertJsonPath('data.files.0.material.id', $material->id)
            ->assertJsonPath('data.files.0.material.name', $material->name)
            ->assertJsonPath('data.files.1', null)
            ->assertJsonPath('data.materials', []);
    }
}
