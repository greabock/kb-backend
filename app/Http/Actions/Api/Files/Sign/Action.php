<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Files\Sign;

use App\Models\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Action
{
    public function __invoke(File $file)
    {
        return [
            'data' => \URL::temporarySignedRoute(
                'files.download', now()->addMinutes(5), ['file' => $file->id]
            ),
        ];
    }
}

