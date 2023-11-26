<?php

use App\Enums\NewsProviderEnum;

return [

    /*
    |--------------------------------------------------------------------------
    | All the news providers
    |--------------------------------------------------------------------------
    | Here I have listed all the news providers that I am using in this project
    | along with their API endpoints and API keys.
    |
    */

    NewsProviderEnum::NEWS_API->value => [
        'api_key' => env('NEWS_NEWS_API_API_KEY'),

        'base_url' => 'https://newsapi.org/v2',
        'article_endpoint' => '/everything',
    ],

    NewsProviderEnum::THE_GUARDIAN->value => [
        'api_key' => env('NEWS_THE_GUARDIAN_API_KEY'),

        'base_url' => 'https://content.guardianapis.com',
        'article_endpoint' => '/search',
    ],

    NewsProviderEnum::NEW_YORK_TIMES->value => [
        'api_key' => env('NEWS_NEW_YORK_TIMES_API_KEY'),

        'base_url' => 'https://api.nytimes.com/svc/search/v2',
        'article_endpoint' => '/articlesearch.json',
    ]
];
