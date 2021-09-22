<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Groups\Create;

use App\Http\Resources\User\GroupResource;
use App\Models\User\Group;
use Greabock\Populator\Populator;


class Action
{
    public function __invoke(Request $request, Populator $populator): GroupResource
    {
        $group = $populator->populate(Group::class, $request->getStruct());
        $populator->flush();

        return new GroupResource($group);
    }
}
