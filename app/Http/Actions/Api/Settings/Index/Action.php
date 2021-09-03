<?php


declare(strict_types=1);

namespace App\Http\Actions\Api\Settings\Index;

class Action
{
    public function __invoke()
    {
        return [
            'colors' => [
                'implement' => '#00000',
                'complement' => '#00000',
            ],
            'logo' => 'somewhere.jpg',
        ];
    }
}
