<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Enums;

use App\Models\Enum;
use Tests\Feature\Api\ActionTestCase;

class UpdateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'enums.update';
    }

    public function testUserCanUpdateTitle(): void
    {
        $oldTitle = 'this is old title!';
        $newTitle = 'this is new title!';

        /** @var Enum $enum */
        $enum = Enum::factory()->create(['title' => $oldTitle]);

        $this->callAuthorizedRouteAction(['title' => $newTitle], ['enum' => $enum->id]);

        $this->assertDatabaseHas('enums', ['id' => $enum->id, 'title' => $newTitle]);
    }

    public function testUserCanAddValuesToEnum(): void
    {
        $valueTitle = 'this is value title!';

        /** @var Enum $enum */
        $enum = Enum::factory()->create();

        $this->callAuthorizedRouteAction(['values' => [
            ['title' => $valueTitle],
        ]], ['enum' => $enum->id]);

        $this->assertDatabaseHas('enum_values', ['title' => $valueTitle]);
    }

    public function testUserCanRemoveValuesToEnum(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(), 'values')->create();
        $enum->refresh();
        $enumValueId = $enum->values->first()->id;

        $this->callAuthorizedRouteAction(['values' => [
        ]], ['enum' => $enum->id]);

        $this->assertDatabaseMissing('enum_values', [
            'deleted_at' => null, 'id' => $enumValueId,
        ]);

        $this->assertDatabaseHas('enum_values', [
            'id' => $enumValueId,
        ]);
    }

    public function testUserCanUpdateTitleOfValueToEnum(): void
    {
        $oldValueTitle = 'this is old value title!';
        $newValueTitle = 'this is new value title!';

        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory(['title' => $oldValueTitle]), 'values')->create();

        $this->callAuthorizedRouteAction(['values' => [
            [
                'id' => $enum->values->first()->id,
                'title' => $newValueTitle,
            ]
        ]], ['enum' => $enum->id]);

        $this->assertDatabaseHas('enum_values', [
            'id' => $enum->values->first()->id,
            'title' => $newValueTitle,
            'enum_id' => $enum->id,
        ]);

        $this->assertDatabaseCount('enum_values', 1);
    }
}
