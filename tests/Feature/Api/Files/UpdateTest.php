<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Files;

use App\Models\File;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class UpdateTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'files.update';
    }

    public function testFileCanBeUpdated()
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
            'extension' => 'doc',
        ]);

        $this->callAuthorizedByAdminRouteAction([
            'name' => 'changed name'
        ], ['file' => $file->id]);

        $this->assertDatabaseHas('files', [
            'name' => 'changed name',
            'id' => $file->id
        ]);
    }
}
