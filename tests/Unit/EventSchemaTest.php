<?php

namespace Tests\Unit;

use App\Domain\Analytics\EventSchema;
use App\Domain\Analytics\EventType;
use Tests\TestCase;

class EventSchemaTest extends TestCase
{
    public function test_it_defines_allowed_scroll_buckets()
    {
        $this->assertSame(['0_25', '25_50', '50_75', '75_100'], EventSchema::SCROLL_BUCKETS);
    }

    public function test_it_allows_only_known_props_for_scroll_depth()
    {
        $this->assertSame(['scroll_bucket'], EventSchema::allowedProps(EventType::ScrollDepth));
        $this->assertSame(['scroll_bucket'], EventSchema::allowedPropsForType('scroll_depth'));
        $this->assertSame([], EventSchema::allowedPropsForType('unknown_type'));
    }

    public function test_it_rejects_unknown_scroll_bucket_values()
    {
        $allowedValues = EventSchema::allowedValuesForTypeProp('scroll_depth', 'scroll_bucket');

        $this->assertIsArray($allowedValues);
        $this->assertContains('75_100', $allowedValues);
        $this->assertNotContains('100_200', $allowedValues);
    }
}
