<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Sections;

use App\Models\Enum;
use Tests\Feature\Api\ActionTestCase;

class CreateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'sections.create';
    }

    public function testUserCanCreateSection(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->create();

        $response = $this->callAuthorizedByAdminRouteAction([
            'title' => 'New section',
            'is_dictionary' => true,
            'is_navigation' => true,
            'sort_index' => 0,
            'fields' => [
                [
                    'title' => 'title',
                    'required' => false,
                    'is_present_in_card' => false,
                    'filter_sort_index' => null,
                    'sort_index' => 0,
                    'type' => [
                        'name' => 'String',
                        'max' => 55,
                        'min' => 0,
                    ]
                ],
                [
                    'title' => 'title',
                    'required' => false,
                    'is_present_in_card' => false,
                    'filter_sort_index' => null,
                    'sort_index' => 0,
                    'type' => [
                        'name' => 'List',
                        'of' => [
                            'name' => 'Enum',
                            'of' => $enum->id,
                        ]
                    ]
                ],
                [
                    'title' => 'deadline',
                    'required' => false,
                    'is_present_in_card' => false,
                    'filter_sort_index' => null,
                    'sort_index' => 0,
                    'type' => [
                        'name' => 'Date',
                    ]
                ],
            ]
        ])
            ->assertCreated();

        $sectionId = $response->json('data.id');
        $field1Id = $response->json('data.fields.0.id');
        $field2Id = $response->json('data.fields.1.id');
        $field3Id = $response->json('data.fields.2.id');

        $this->assertTrue(\Schema::hasTable('sections.' . $sectionId));
        $this->assertSame('string', \Schema::getColumnType('sections.' . $sectionId, $field1Id));
        $this->assertSame('datetime', \Schema::getColumnType('sections.' . $sectionId, $field3Id));

        $this->assertTrue(\Schema::hasTable('pivots.' . $field2Id));

        $response = $this->callRouteAction([
            'title' => 'New section',
            'is_dictionary' => true,
            'is_navigation' => true,
            'sort_index' => 0,
            'fields' => [
                [
                    'title' => 'title',
                    'required' => false,
                    'is_present_in_card' => false,
                    'sort_index' => 0,
                    'filter_sort_index' => null,
                    'type' => [
                        'name' => 'List',
                        'of' => [
                            'name' => 'Dictionary',
                            'of' => $sectionId,
                        ]
                    ]
                ],
            ]
        ])->assertCreated();

        $field1Id = $response->json('data.fields.0.id');
        $this->assertTrue(\Schema::hasTable('pivots.' . $field1Id));
    }
}
