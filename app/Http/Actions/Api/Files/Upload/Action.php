<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Files\Upload;

use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileManager;

class Action
{
    public function __invoke(Request $request, FileManager $files)
    {
        return FileResource::collection(array_map(function ($file) use ($request, $files) {
            $indexed = true;
            [$id, $realpath, $url, $content] = $files->store(
                $file->getContent(),
                $file->getClientOriginalExtension(),
                $request->has('field')
            );

            $name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            return File::create(compact(
                'id',
                'realpath',
                'url',
                'indexed',
                'content',
                'name',
                'extension',
            ));

        }, $request->file('files')));
    }
}
