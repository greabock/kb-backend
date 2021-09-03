<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Create;

use App\Http\Resources\EnumResource;
use App\Models\Enum;
use Greabock\Populator\Populator;

class Action
{
    public function __invoke(Request $request, Populator $populator)
    {
        /** @var Enum $enum */
        $enum = $populator->populate(Enum::class, $request->all());

        $populator->flush();

        return new EnumResource($enum);
    }
}
