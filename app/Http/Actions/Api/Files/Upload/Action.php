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
        return FileResource::collection(array_map(function ($file) use ($files) {
            $indexed = true;
            [$realpath, $url, $content] = $files->store($file->getContent(), $file->getExtension());
            return new File(compact('realpath', 'url', 'indexed', 'content'));
        }, $request->file('files')));
    }
}
