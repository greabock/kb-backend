<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Section\Field;
use App\Services\ColumnBuilder;

class SectionFieldObserver
{
    private ColumnBuilder $columnBuilder;

//    public function __construct(ColumnBuilder $columnBuilder)
//    {
//        $this->columnBuilder = $columnBuilder;
//    }
//
//    public function updating(Field $field)
//    {
//        if ($field->wasChanged(['type'])) {
//            $this->columnBuilder->drop($field);
//        }
//    }
//
//    public function created(Field $field): void
//    {
//        $this->columnBuilder->build($field);
//    }
//
//    public function updated(Field $field): void
//    {
//        $this->columnBuilder->build($field);
//    }
//
//    public function deleted(Field $field): void
//    {
//        $this->columnBuilder->drop($field);
//    }
}
