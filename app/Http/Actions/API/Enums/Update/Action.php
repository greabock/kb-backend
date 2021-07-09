<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Update;


use App\Http\Resources\EnumResource;
use App\Models\Enum;
use Greabock\Populator\Populator;

class Action
{
    public function __invoke(Enum $enum, Request $request, Populator $populator): EnumResource
    {
        $populator->populate($enum, $request->only('title', 'values'));

        $populator->flush();

        return new EnumResource($enum);
    }
}
