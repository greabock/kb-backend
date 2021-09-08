<?php

declare(strict_types=1);

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    public const INCLUDE = 0;
    public const EXCEPT = 1;

    public function target()
    {
        return $this->morphToMany();
    }
}
