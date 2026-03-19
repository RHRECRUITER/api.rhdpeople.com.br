<?php

namespace App\Domain\Analytics;

enum EventType: string
{
    case SectionView = 'section_view';
    case ScrollDepth = 'scroll_depth';
    case FormStart = 'form_start';
    case FormSubmitSuccess = 'form_submit_success';
    case PageView = 'page_view';
    case Heartbeat = 'heartbeat';
    case RestrictedClick = 'restricted_click';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $case) => $case->value,
            self::cases()
        );
    }
}

