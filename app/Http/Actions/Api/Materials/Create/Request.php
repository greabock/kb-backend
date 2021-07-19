<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Create;

use App\Http\Actions\Api\ApiRequest;
use App\Models\Material;
use App\Models\Section;

/**
 * @property Section|string $section
 */
class Request extends ApiRequest
{
    public function rules(): array
    {
        dump($this->resolveSection()->rules());

        return array_merge(
            ['name' => 'required|string|max:255'],
            $this->resolveSection()->rules(),
        );
    }

    public function struct(): ?array
    {
        return $this->resolveSection()->struct();
    }

    public function resolveSection(): Section
    {
        return $this->section instanceof Section ? $this->section : $this->section = Section::findOrFail($this->section);
    }
}
