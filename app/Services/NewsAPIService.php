<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Contracts\NewsService;
use App\Traits\ImportArticles;

class NewsAPIService implements NewsService
{
    use ImportArticles;

    private array $config;

    public ?string $query = null;
    public int $pageNo = 1;
    public int $pageSize = 100;

    public ?string $from = null;
    public ?string $to = null;

    function __construct()
    {
        $this->config = config('news.news_api');
    }

    /**
     * Sets the start date for the articles.
     * @param Carbon $from
     * @return $this
     */
    public function from(Carbon $from): self
    {
        $this->from = $from->format('Y-m-d');
        return $this;
    }

    /**
     * Sets the end date for the articles.
     * @param Carbon $to
     * @return $this
     */
    public function to(Carbon $to): self
    {
        $this->to = $to->format('Y-m-d');
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
     * @param int $pageSize max 100
     * @return self
     */
    public function pageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * Sets the page number.
     * @param int $pageNo
     * @return self
     */
    public function pageNo(int $pageNo): self
    {
        $this->pageNo = $pageNo;
        return $this;
    }

    public function getArticles(): array
    {
        try {
            $articles = Http::get($this->config['base_url'] . $this->config['article_endpoint'], [
                'apiKey' => $this->config['api_key'],
                'q' => $this->query,
                'from' => $this->from,
                'to' => $this->to,
                'pageSize' =>  $this->pageSize,
                'page' => $this->pageNo,
                'domains' => !$this->query ? 'bbc.co.uk,techcrunch.com,thenextweb.com' : null, //query or domain one of them is required for search
            ])->throw()->json('articles');

            return $this->normalizeData($articles);
        } catch (\Exception $e) {
            Log::error("Error while fetching news from NewsAPI: {$e->getMessage()}");
        }
        return [];
    }

    private function normalizeData(array $articles): array
    {
        return array_map(function ($v) {
            return [
                'author' => $v['author'] ?? null,
                'source' => $v['source']['name'] ?? null,
                'title' => $v['title'],
                'content' => $v['content'] ?? null,
                'description' => $v['description'] ?? null,
                'image_url' => $v['urlToImage'] ?? null,
                'published_at' => $v['publishedAt'] ? Carbon::parse($v['publishedAt']) : Carbon::now(),
                'source_url' => $v['url'] ?? null,
            ];
        }, $articles);
    }
}
