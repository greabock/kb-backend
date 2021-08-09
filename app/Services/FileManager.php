<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Ramsey\Uuid\Uuid;
use Storage;
use Stringable;
use Vaites\ApacheTika\Client as TikaClient;

class FileManager
{
    public function __construct(
        private Filesystem $fs,
        private TikaClient $tika,
    )
    {
    }

    public function store(
        Stringable|string $content,
        string $contentType,
        bool $index = false,
        string $id = null,
    ): array
    {
        $id = $id ?? Uuid::uuid4()->toString();
        Storage::disk('local')->put("upload/$id.$contentType", $content);
        $path = Storage::disk('local')->path("upload/$id.$contentType");
        return [
            $id,
            $path,
            route('files.download', [$id], true),
            $index ? $this->tika->getText($path) : null
        ];
    }
}
