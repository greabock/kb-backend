<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Materials;

use App\Events\Handlers\UpdateDatabaseOnSectionUpdated;
use App\Events\SectionUpdated;
use App\Jobs\UpdateMaterialClass;
use App\Jobs\UpdateSectionIndex;
use App\Models\Enum;
use App\Models\File;
use App\Models\Material;
use App\Models\Section;
use Greabock\Populator\Populator;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class CreateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'sections.materials.create';
    }

    public function testUserCanCreateMaterialWithString(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory([
                'type' => ['name' => 'String']
            ]), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'name' => 'test name',
            $section->fields->first()->id => 'Привет!',
        ], ['section' => $section->id])
            ->assertCreated();
    }

    public function testUserCanCreateMaterialWithDefinedId(): void
    {
        /** @var Section $section */
        $section = Section::factory()->create();

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'id' => Uuid::uuid4()->toString(),
            'name' => 'test name',
        ], ['section' => $section->id])
            ->assertCreated();
    }

    public function testUserCanCreateMaterialWithEnum(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();
        $materialName = 'test material name';

        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory([
                'type' => [
                    'name' => 'Enum',
                    'of' => $enum->id,
                ]
            ]), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'name' => $materialName,
            $section->fields->first()->id => ['id' => $enum->values->first()->id],
        ], ['section' => $section->id])
            ->assertCreated()
            ->assertJsonPath('data.name', $materialName)
            ->assertJsonPath("data.{$section->fields->first()->id}.title", $enum->values->first()->title)
            ->assertJsonPath("data.{$section->fields->first()->id}.id", $enum->values->first()->id);
    }

    public function testMaterialCanLinkOtherMaterial(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();
        $materialName = 'test material name';

        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Enum', 'of' => $enum->id,]]), 'fields')
            ->create();

        $section->refresh();

        /** @var Populator $populator */
        $populator = $this->app[Populator::class];

        /** @var Material $material */
        $material = $populator->populate($section->class_name, [
            'name' => $materialName,
            $section->fields->first()->id => ['id' => $enum->values->first()->id],
        ]);

        $populator->flush();

        /** @var Section $section */
        $section2 = Section::factory()
            ->has(Section\Field::factory(['type' => ['name' => 'Dictionary', 'of' => $section->id]]))
            ->create();
        $section2->refresh();

        $this->assertDatabaseHas('sections.' . $material->sectionId, [
            'id' => $material->id,
        ]);

        $this->callAuthorizedByAdminRouteAction([
            'name' => $materialName,
            $section2->fields->first()->id => ['id' => $material->id],
        ], ['section' => $section2->id])
            ->assertCreated();
    }


    public function testUserCanCreateMaterialWithMultipleLinks(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();
        $materialName = 'test material name';

        /** @var Section $section */
        $section = Section::factory()
            ->has(Section\Field::factory(['type' => [
                'name' => 'List',
                'of' => [
                    'name' => 'Enum',
                    'of' => $enum->id,
                ]
            ]]), 'fields')
            ->create();

        $section->refresh();


        $this->callAuthorizedByAdminRouteAction([
            'name' => $materialName,
            $section->fields->first()->id => [['id' => $enum->values->first()->id], ['id' => $enum->values->first()->id]],
        ], ['section' => $section->id])
            ->assertCreated();
    }

    public function testRequiredFields(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(), 'fields'
        )->create();

        $section->refresh();
        $this->callAuthorizedByAdminRouteAction([], ['section' => $section->id])
            ->assertJsonValidationErrors([$section->fields->first()->id, 'name']);
    }

    public function testNotRequiredFields(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['required' => false]), 'fields'
        )->create();

        $section->refresh();
        $this->callAuthorizedByAdminRouteAction([], ['section' => $section->id])
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors([$section->fields->first()->id]);
    }

    public function testSelectField(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['type' => [
                'name' => 'Select',
                'of' => ['one', 'other']
            ]]), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'name' => 'test',
            $section->fields->first()->id => 'one'
        ], ['section' => $section->id])
            ->assertCreated();

        $this->assertDatabaseHas($section->table_name, [$section->fields->first()->id => 'one']);
    }

    public function testMultiSelectField(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['type' => [
                'name' => 'List',
                'of' => [
                    'name' => 'Select',
                    'of' => ['one', 'other']
                ]
            ]]), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'name' => 'test',
            $section->fields->first()->id => ['one']
        ], ['section' => $section->id])
            ->assertCreated();
    }


    public function testMultipleEnums(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();

        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['type' => [
                'name' => 'List',
                'of' => [
                    'name' => 'Enum',
                    'of' => $enum->id,
                ]
            ]]), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'name' => 'test',
            $section->fields->first()->id => [['id' => $enum->values->first()->id]]
        ], ['section' => $section->id])
            ->assertJsonPath('data.' . $section->fields->first()->id . '.0.id', $enum->values->first()->id)
            ->assertCreated();
    }

    public function testCreateMaterialWithFile(): void
    {
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


        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['type' => [
                'name' => 'File',
            ]]), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'name' => 'test',
            $section->fields->first()->id => ['id' => $file->id]
        ], ['section' => $section->id])
            ->assertJsonPath('data.' . $section->fields->first()->id . '.id', $file->id)
            ->assertCreated();
    }


    public function testCreateMaterialWithMultipleFiles(): void
    {
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

        $file2 = File::create([
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

        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['type' => [
                'name' => 'List',
                'of' => [
                    'name' => 'File',
                ]
            ]]), 'fields'
        )->create();

        $section->refresh();

        $this->callAuthorizedByAdminRouteAction([
            'name' => 'test',
            $section->fields->first()->id => [
                ['id' => $file->id],
                ['id' => $file2->id],
            ]
        ], ['section' => $section->id])
            ->assertCreated();
    }


    public function testCreateMaterialWithMultipleFilesIgnoreExtraFields(): void
    {
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

        $file2 = File::create([
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

        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['type' => [
                'name' => 'List',
                'of' => [
                    'name' => 'File',
                ]
            ]]), 'fields'
        )->create();

        $section->refresh();
        $this->callAuthorizedByAdminRouteAction([
            'name' => 'test',
            $section->fields->first()->id => [
                ['id' => $file->id, 'name' => $file->name],
                ['id' => $file2->id, 'name' => $file2->name],
            ]
        ], ['section' => $section->id])
            ->dump()
            ->assertCreated()
            ->assertJsonPath('data.' . $section->fields->first()->id. '.0.id', $file->id)
            ->assertJsonPath('data.' . $section->fields->first()->id. '.1.id', $file2->id)
            ;
    }
}

