<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Files\Upload;

use App\Models\File;
use App\Services\FileManager;

class Action
{
    public function __invoke(Request $request, FileManager $files)
    {
        $fs = [];
        $indexed = false;

        foreach ($request->file('files') as $file) {
            [$realpath, $url] = $files->store($file->getContent(), $file->getExtension());
            $fs[] = new File(compact('realpath', 'url', 'indexed'));
        }

        return [
            'data' => $fs
        ];
    }
}
