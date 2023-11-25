<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Contracts\NewsService;
use App\Enums\NewsProviderEnum;

class NewYorkTimesService implements NewsService
{
    private string $config;

    public ?string $query;
    public int $pageNo = 1;
    public $pageSize = null;

    public ?string $from;
    public ?string $to;

    function __construct()
    {
        $this->config = config('news.new_york_times');
    }

    /**
     * Sets the start date for the articles.
     * @param Carbon $from
     * @return $this
     */
    public function from(Carbon $from): self
    {
        $this->from = $from->format('Ymd');
        return $this;
    }

    /**
     * Sets the end date for the articles.
     * @param Carbon $to
     * @return $this
     */
    public function to(Carbon $to): self
    {
        $this->to = $to->format('Ymd');
        return $this;
    }

    /**
     * Search for a given query string.
     * @param string $query The query to search for.
     * @return self
     */
    public function search(string $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Sets the page size.
     * @param int $pageSize The page size.
     * @deprecated Not supported by the New York Times API.
     * @return self
     */
    public function pageSize(int $pageSize): self
    {
        $this->pageSize = null;
        return $this;
    }

    /**
     * Sets the page number.
     * @param int $pageNo The page number.
     * @return self
     */
    public function pageNo(int $pageNo): self
    {
        $this->pageNo = $pageNo;
        return $this;
    }

    public function getNews(): array
    {
        try {
            $articles = Http::get($this->config['url'] . $this->config['article_endpoint'], [
                'api-key' => $this->config['api_key'],
                'q' => $this->query,
                'begin_date' => $this->from,
                'end_date' => $this->to,
                'page' => $this->pageNo,
            ])->throw()->json('response.docs');

            return $this->normalizeData($articles);
        } catch (\Exception $e) {
            Log::error("Error while fetching news from New Yok Times: {$e->getMessage()}");
        }
        return [];
    }

    private function normalizeData(array $articles): array
    {
        return array_map(function ($v) {
            return [
                'author' => !empty($v['byline']['person']) ?  preg_replace('/\s+/', ' ', $v['byline']['person'][0]['firstname'] . " " . $v['byline']['person'][0]['middlename'] . " " . $v['byline']['person'][0]['lastname']) : null,
                'source' => $v['source'] ?? null,
                'title' => $v['headline']['main'] ?? null,
                'content' => $v['lead_paragraph'] ?? null,
                'image' => !empty($v['multimedia'][0]['url']) ? "https://www.nytimes.com/" . $v['multimedia'][0]['url'] :  null,
                'published_at' => $v['pub_date'] ?? null,
                'url' => $v['web_url'] ?? null,
                'news_provider' => NewsProviderEnum::NEW_YORK_TIMES->value,
            ];
        }, $articles);
    }
}
