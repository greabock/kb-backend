<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Files\Download;

use App\Models\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Action
{
    public function __invoke(File $file): BinaryFileResponse
    {
        return response()->download($file->realpath, $file->name);
    }
}
