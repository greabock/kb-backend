<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Section;
use App\Services\SectionMaterialModelBuilder;
use App\Services\TableBuilder;
use Artisan;
use DB;

class SectionObserver
{
    private SectionMaterialModelBuilder $modelBuilder;
    private TableBuilder $tableBuilder;

    public function __construct(
        SectionMaterialModelBuilder $modelBuilder,
        TableBuilder $tableBuilder,
    )
    {
        $this->modelBuilder = $modelBuilder;
        $this->tableBuilder = $tableBuilder;
    }

    public function created(Section $section)
    {
        $this->tableBuilder->create($section);
    }

    public function saved(Section $section)
    {
        $this->modelBuilder->remember($section);
    }

    public function deleting(Section $section): bool
    {
        DB::beginTransaction();

        try {

            $section->fields->each->delete();
            $this->tableBuilder->drop($section);

        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }

        DB::commit();

        return true;
    }
}
