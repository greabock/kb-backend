<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Files;

use App\Models\Section;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\Feature\Api\ActionTestCase;

class UploadTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'files.upload';
    }

    public function testUserCanUploadFile()
    {
        $section = Section::factory()->has(Section\Field::factory([
            'type' => [
                'name' => 'List',
                'of' => [
                    'name' => 'File',
                    'extensions' => ['doc', 'docx'],
                ]
            ]
        ]), 'fields')->create();

        $section->refresh();

        \Auth::login(User::factory()->create());

        $this->post(route($this->getRouteName()), [
            'field' => ['id' => $section->fields->first()->id],
            'files' => [
                UploadedFile::fake()->create('document.docx', 255),
            ]
        ], ['accept' => 'application/json'])
            ->dump()
            ->assertOk();
    }
}
