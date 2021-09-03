<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Materials;

use App\Models\File;
use App\Models\Material;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class ShowTest extends ActionTestCase
{
    use WithFaker;

    public function getRouteName(): string
    {
        return 'sections.materials.show';
    }

    public function testUserCanSeeMaterialDetails(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory()->count(3),
            'fields',
        )->create();

        $section->refresh();

        $fieldValues = $section->fields->keyBy('id')->map(fn() => $this->faker->word)
            ->toArray();

        /** @var Material $material */
        $material = $this->populator()->populate(
            $section->class_name,
            array_merge(['name' => 'Name'], $fieldValues)
        );

        $this->populator()->flush();

        $this->callAuthorizedRouteAction([], ['material' => $material->id, 'section' => $section->id])
            ->assertJsonStructure(['data' => array_keys($fieldValues)])
            ->assertOk();
    }

    public function testMaterialReturnWithRelations(): void
    {
        /** @var Section $section */
        $section = Section::factory()->has(
            Section\Field::factory(['type' => ['name' => 'List', 'of' => ['name' => 'File']]]),
            'fields',
        )->create();

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

        /** @var Material $material */
        $material = $this->populator()->populate(
            $section->class_name,
            [
                'name' => 'Name',
                $section->fields->first()->id => [['id' => $file->id]]
            ]
        );

        $this->populator()->flush();


        $this->callAuthorizedRouteAction([], ['material' => $material->id, 'section' => $section->id])
            ->assertOk()
            ->assertJsonPath('data.' . $section->fields->first()->id . '.0.id', $file->id)
            ->assertJsonPath('data.' . $section->fields->first()->id . '.0.name', $file->name)
            ->assertJsonPath('data.' . $section->fields->first()->id . '.0.extension', $file->extension)
        ;
    }
}
