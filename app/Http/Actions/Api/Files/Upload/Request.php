<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Files\Upload;

use App\Http\Actions\Api\ApiRequest;
use App\Models\Section\Field;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class Request extends ApiRequest
{
    private ?Field $fieldModel = null;

    public function rules(): array
    {
        return [
            'field' => 'required',
            'field.id' => [
                'required',
                Rule::exists('section_fields', 'id')->where(function (Builder $query) {
                    return $query
                        ->whereJsonContains('type->name', 'File')
                        ->orWhere(function (Builder $query) {
                            $query->whereJsonContains('type->name', 'List');
                            $query->whereJsonContains('type->of->name', 'File');
                        });
                }),
            ],
            'files' => ['required', 'array', ...$this->getLengthRules()],
            'files.*' => ['required', ...$this->getMimeRule()],
        ];
    }

    private function getLengthRules(): array
    {

        $rules = ['min:1'];

        if (
            ($field = $this->getField()) &&
            ($field->type['name'] === 'File')
        ) {
            $rules[] = 'max:1';
        }

        return $rules;
    }

    private function getMimeRule(): array
    {
        $rules = [];

        if ($field = $this->getField()) {
            $type = $field->type['name'] === 'List' ?
                $field->type['of'] : $field->type;

            if (isset($type['extensions'])) {
                $rules[] = 'mimes:' . implode(',', $type['extensions']);
            }
        }

        return $rules;
    }

    private function getField(): ?Field
    {
        if ($this->has('field') && isset($this->get('field')['id'])) {
            $this->fieldModel = Field::find($this->get('field')['id']);
        }

        return $this->fieldModel;
    }
}
