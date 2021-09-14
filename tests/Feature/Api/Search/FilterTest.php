<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Search;

use App\Jobs\CreateMaterialDocument;
use App\Models\Enum;
use App\Models\File;
use App\Models\Material;
use App\Models\Section;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class FilterTest extends ActionTestCase
{
    use WithFaker;

    public function getRouteName(): string
    {
        return 'search.filter';
    }

    public function testUserCanFilterInMaterial()
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

        $this->callAuthorizedRouteAction(['search' => 'удобный'], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material->id);
    }

    public function testUserCanFilterWithOneFile()
    {
        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['title' => 'My file', 'type' => ['name' => 'File']]), 'fields')
            ->create();

        $section->refresh();


        $file = File::create([
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

        $this->callAuthorizedRouteAction(['search' => 'удобный'], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.files.0.material.id', $material->id)
            ->assertJsonPath('data.files.0.material.name', $material->name)
            ->assertJsonPath('data.files.0.section.id', $section->id)
            ->assertJsonPath('data.files.0.file.id', $file->id);
    }


    public function testUserCanFilterWithMultipleFiles()
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

        $this->callAuthorizedRouteAction(['search' => 'трактат'], ['section' => $section->id])
            ->assertOk();
    }


    public function testUserCanFilterWithFilterExtensions()
    {
        /** @var Section $section */
        $section = Section::factory()->has(Section\Field::factory([
            'title' => 'My file',
            'type' => [
                'name' => 'List',
                'of' => ['name' => 'File']
            ]
        ]), 'fields')->create();

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

        $this->callAuthorizedRouteAction(['search' => 'трактат', 'extensions' => ['doc']], ['section' => $section->id])
            ->assertJsonCount(1, 'data.files')
            ->assertJsonPath('data.files.0.file.extension', 'doc')
            ->assertOk();
    }

    public function testUserCanFilterSelect(): void
    {
        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Select', 'of' => ['one', 'two']]]), 'fields')
            ->create();

        $section->refresh();

        $data = [
            'name' => 'Об особенностях Laravel',
            $section->fields->first()->id => 'two',
        ];

        /** @var Material $material1 */
        $material1 = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material1->id)), 'handle']);

        $data = [
            'name' => 'Об особенностях Laravel',
            $section->fields->first()->id => 'one',
        ];

        /** @var Material $material1 */
        $material2 = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material2->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'Laravel', 'filter' => [
            $section->fields->first()->id => ['two'],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material1->id)
            ->assertJsonPath('data.materials.0.material.' . $section->fields->first()->id, $material1->{$section->fields->first()->id})
            ->assertJsonPath('data.materials.1', null);


        $this->callRouteAction(['search' => 'Laravel', 'filter' => [
            $section->fields->first()->id => ['one'],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material2->id)
            ->assertJsonPath('data.materials.0.material.' . $section->fields->first()->id, $material2->{$section->fields->first()->id})
            ->assertJsonPath('data.materials.1', null);

        $this->callRouteAction(
            [
                'search' => 'Laravel',
                'filter' => [$section->fields->first()->id => ['one', 'two']],
                'sort' => ['field' => 'created_at', 'direction' => 'desc']
            ]
            , ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material2->id)
            ->assertJsonPath('data.materials.0.material.' . $section->fields->first()->id, $material2->{$section->fields->first()->id})
            ->assertJsonPath('data.materials.1.material.id', $material1->id)
            ->assertJsonPath('data.materials.1.material.' . $section->fields->first()->id, $material1->{$section->fields->first()->id});
    }


    public function testUserCanFilterEnum()
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory()->count(2), 'values')->create();
        $enum->refresh();

        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'List', 'of' => [
                'name' => 'Enum',
                'of' => $enum->id,
            ]]]), 'fields')
            ->create();

        $section->refresh();

        $data = [
            'name' => 'Об особенностях Laravel',
            $section->fields->first()->id => [
                ['id' => $enum->values->first()->id],
            ],
        ];

        /** @var Material $material1 */
        $material1 = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material1->id)), 'handle']);

        sleep(1);

        $data = [
            'name' => 'Об особенностях Laravel',
            $section->fields->first()->id => [
                ['id' => $enum->values->last()->id],
            ]
        ];

        /** @var Material $material2 */
        $material2 = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material2->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'Laravel', 'filter' => [
            $section->fields->first()->id => [$enum->values->last()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material2->id)
            ->assertJsonPath('data.materials.1', null);

        $this->callRouteAction(['search' => 'Laravel', 'filter' => [
            $section->fields->first()->id => [$enum->values->first()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material1->id)
            ->assertJsonPath('data.materials.1', null);

        $this->callRouteAction(
            [
                'search' => 'Laravel',
                'filter' => [$section->fields->first()->id => [$enum->values->first()->id, $enum->values->last()->id]],
                'sort' => ['field' => 'created_at', 'direction' => 'desc']
            ]
            , ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material2->id)
            ->assertJsonPath('data.materials.1.material.id', $material1->id);
    }

    public function testUserCanFilterByTwoEnums()
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory()->count(2), 'values')->create();
        $enum->refresh();

        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'List', 'of' => [
                'name' => 'Enum',
                'of' => $enum->id,
            ]]])->count(2), 'fields')
            ->create();

        $section->refresh();

        $data = [
            'name' => 'Об особенностях Laravel',
            $section->fields->first()->id => [
                ['id' => $enum->values->first()->id],
            ],
            $section->fields->last()->id => [
                ['id' => $enum->values->last()->id],
            ],
        ];

        /** @var Material $material1 */
        $material1 = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material1->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'Laravel', 'filter' => [
            $section->fields->first()->id => [$enum->values->last()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials', []);

        $this->callRouteAction(['search' => '', 'filter' => [
            $section->fields->first()->id => [$enum->values->first()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material1->id);


        $this->callRouteAction(['search' => '', 'filter' => [
            $section->fields->last()->id => [$enum->values->last()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material1->id);


        $this->callRouteAction(['search' => '', 'filter' => [
            $section->fields->last()->id => [$enum->values->last()->id],
            $section->fields->first()->id => [$enum->values->first()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material1->id);


        $this->callRouteAction(['search' => '', 'filter' => [
            $section->fields->last()->id => [$enum->values->last()->id, $enum->values->first()->id],
            $section->fields->first()->id => [$enum->values->first()->id, $enum->values->last()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material1->id);


        $this->callRouteAction(['search' => '', 'filter' => [
            $section->fields->last()->id => [$enum->values->first()->id],
            $section->fields->first()->id => [$enum->values->last()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', null);
    }


    public function testUserCanFilterNullFields()
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory()->count(2), 'values')->create();
        $enum->refresh();

        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'List', 'of' => [
                'name' => 'Enum',
                'of' => $enum->id,
            ]]])->count(2), 'fields')
            ->create();

        $section->refresh();

        $data = [
            'name' => 'Об особенностях Laravel',
        ];

        /** @var Material $material1 */
        $material1 = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material1->id)), 'handle']);

        $this->callAuthorizedRouteAction(['search' => 'Laravel', 'filter' => [
            $section->fields->first()->id => [$enum->values->last()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials', []);

        $this->callRouteAction(['search' => 'Laravel', 'filter' => [
            $section->fields->first()->id => [$enum->values->last()->id, $enum->values->first()->id],
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials', []);
    }


    public function testWhenFiltersSelectedFilesIsEmpty()
    {
        /** @var Section $section */
        $section = Section::factory()->has(Section\Field::factory([
            'title' => 'My file',
            'type' => [
                'name' => 'List',
                'of' => ['name' => 'File']
            ]
        ]), 'fields')->create();

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

        $this->callAuthorizedRouteAction(['filter' => [
            'test' => 'test'
        ]], ['section' => $section->id])
            ->assertJsonCount(0, 'data.files')
            ->assertOk();
    }

    public function testUserCanFilterByDate()
    {
        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Date']]), 'fields')
            ->create();

        $section->refresh();

        $data = ['name' => 'Об особенностях Laravel'];

        foreach ($section->fields as $field) {
            $data[$field->id] = now();
        }

        /** @var Material $material */
        $material = $this->populator()->populate($section->class_name, $data);
        $this->populator()->flush();
        $this->app->call([(new CreateMaterialDocument($section->class_name, $material->id)), 'handle']);

        $this->callAuthorizedRouteAction(['filter' => [
            $field->id => [now()->subDay()->format(DATE_W3C), now()->addDay()->format(DATE_W3C)]
        ]], ['section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.materials.0.material.id', $material->id);
    }
}
