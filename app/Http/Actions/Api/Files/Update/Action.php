<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Files\Update;

use App\Http\Resources\FileResource;
use App\Models\File;

class Action
{
    public function __invoke(File $file, Request $request): FileResource
    {
        $file->fill($request->getStruct());
        $file->save();

        return new FileResource($file);
    }
}
