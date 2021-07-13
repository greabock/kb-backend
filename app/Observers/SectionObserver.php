<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Section;
use DB;

class SectionObserver
{
    public function created(Section $section)
    {
        $section->build();
    }

    public function deleting(Section $section): bool
    {
        DB::beginTransaction();

        try {

            $section->fields->each->delete();
            $section->drop();

        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }

        DB::commit();

        return true;
    }
}
