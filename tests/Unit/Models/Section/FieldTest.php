<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Section;

use App\Models\Section\Field;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FieldTest extends TestCase
{
    use WithFaker;

    public function testValidRelationInfo()
    {
        $enum = new Field(['type' => ['name' => 'Enum', 'of' => $this->faker->uuid()]]);
        $this->assertTrue($enum->isRelationField());
        $this->assertTrue($enum->isBelongsTo());
        $this->assertFalse($enum->isBelongsToMany());
        $this->assertFalse($enum->isPlainField());

        $dictionary = new Field(['type' => ['name' => 'Dictionary', 'of' => $this->faker->uuid()]]);
        $this->assertTrue($dictionary->isRelationField());
        $this->assertTrue($dictionary->isBelongsTo());
        $this->assertFalse($dictionary->isBelongsToMany());
        $this->assertFalse($dictionary->isPlainField());

        $file = new Field(['type' => ['name' => 'File']]);
        $this->assertTrue($file->isRelationField());
        $this->assertTrue($file->isBelongsTo());
        $this->assertFalse($file->isBelongsToMany());
        $this->assertFalse($file->isPlainField());

        $listOfEnum = new Field(['type' => ['name' => 'List', 'of' => ['name' => 'Enum', 'of' => $this->faker->uuid()]]]);
        $this->assertTrue($listOfEnum->isRelationField());
        $this->assertFalse($listOfEnum->isBelongsTo());
        $this->assertTrue($listOfEnum->isBelongsToMany());
        $this->assertFalse($listOfEnum->isPlainField());

        $listOfDictionary = new Field(['type' => ['name' => 'List', 'of' => ['name' => 'Dictionary', 'of' => $this->faker->uuid()]]]);
        $this->assertTrue($listOfDictionary->isRelationField());
        $this->assertFalse($listOfDictionary->isBelongsTo());
        $this->assertTrue($listOfDictionary->isBelongsToMany());
        $this->assertFalse($listOfDictionary->isPlainField());

        $listOfFile = new Field(['type' => ['name' => 'List', 'of' => ['name' => 'File']]]);
        $this->assertTrue($listOfFile->isRelationField());
        $this->assertFalse($listOfFile->isBelongsTo());
        $this->assertTrue($listOfFile->isBelongsToMany());
        $this->assertFalse($listOfFile->isPlainField());

        $listOfSelect = new Field(['type' => ['name' => 'List', 'of' => ['name' => 'Select', 'of' => ['test']]]]);
        $this->assertFalse($listOfSelect->isRelationField());
        $this->assertFalse($listOfSelect->isBelongsTo());
        $this->assertFalse($listOfSelect->isBelongsToMany());
        $this->assertTrue($listOfSelect->isPlainField());


        $select = new Field(['type' => ['name' => 'Select', 'of' => ['test']]]);
        $this->assertFalse($select->isRelationField());
        $this->assertFalse($select->isBelongsTo());
        $this->assertFalse($select->isBelongsToMany());
        $this->assertTrue($select->isPlainField());
    }
}
