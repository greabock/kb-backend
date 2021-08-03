<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Ramsey\Uuid\Uuid;
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
        string $id = null,
    ): array
    {
        $id = $id ?? Uuid::uuid4()->toString();

        $this->fs->put("upload/$id.$contentType", $content);
        $path = $this->fs->path("upload/$id.$contentType");

        return [
            $path,
            route('files.content', [$id], true),
            $this->tika->getText($path)
        ];
    }
}
