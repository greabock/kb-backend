<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Create;

use App\Models\Section;
use Greabock\Populator\Populator;


class Action
{
    public function __invoke(Section $section, Request $request, Populator $populator)
    {
        $material = $populator->populate($section->getFQCN(), $request->getStruct());

        $populator->flush();

        return response()->json([
            'data' => $material->toArray()
        ])->setStatusCode(201);
    }
}
