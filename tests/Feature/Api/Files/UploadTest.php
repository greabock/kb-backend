<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Files;

use App\Models\Section;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Session\Store;
use Storage;
use Tests\Feature\Api\ActionTestCase;

class UploadTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'files.upload';
    }

    public function testUserCanUploadFileForField()
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


    public function testUserCanUploadFileWithoutField()
    {
        \Auth::login(User::factory()->create());

        $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        $response = $this->post(route($this->getRouteName()), [
            'files' => [UploadedFile::fake()->create('CV.docx', 0, $mime)],
        ], ['accept' => 'application/json'])
            ->dump()
            ->assertOk();

        $this->assertDatabaseHas('files', [
            'id' => $response->json('data.0.id')
        ]);

        $this->assertTrue(Storage::disk()->exists('uploads/' . $response->json('data.0.id') . '.docx'));
    }

}
