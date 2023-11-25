<?php

namespace App\Enums;

enum NewsProviderEnum: string
{
    case NEWS_API = 'news_api';
    case THE_GUARDIAN = 'the_guardian';
    case NEW_YORK_TIMES = 'new_york_times';

    public static function getAllValues(): array
    {
        return array_column(Self::cases(), 'value');
    }
}
