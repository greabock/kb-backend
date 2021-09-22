<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Groups\Update;

use App\Http\Actions\Api\Groups\Create\Request;
use App\Http\Resources\User\GroupResource;
use App\Models\User\Group;
use Greabock\Populator\Populator;

class Action
{
    public function __invoke(Group $group, Populator $populator, Request $request): GroupResource
    {
        $populator->populate($group, $request->all());
        $populator->flush();

        return new GroupResource($group);
    }
}
