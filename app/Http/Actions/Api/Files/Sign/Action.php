<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Files\Sgin;

use App\Models\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Action
{
    public function __invoke(File $file)
    {
        return [
            'data' => return \URL::temporarySignedRoute(
                'files.download', now()->addMinutes(5), ['user' => $file->id]
            ),
        ];
    }
}

