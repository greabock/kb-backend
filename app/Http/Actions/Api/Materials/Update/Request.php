<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Update;

use App\Http\Actions\Api\ApiRequest;
use App\Models\Section;

class Request extends ApiRequest
{
    public function rules(): array
    {
        return array_merge(
            ['name' => 'sometimes|string|max:255'],
            $this->resolveSection()->rules(required: false),
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
