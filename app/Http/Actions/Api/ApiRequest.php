<?php

declare(strict_types=1);

namespace App\Http\Actions\Api;

use Illuminate\Foundation\Http\FormRequest;

abstract class ApiRequest extends FormRequest
{
    public function struct(): ?array
    {
        return null;
    }

    public function getStruct()
    {
        $data = $this->all();

        if ($struct = $this->struct()) {
            return data_get_struct($data, $struct);
        }

        return $data;
    }
}
