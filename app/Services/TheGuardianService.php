<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Contracts\NewsService;
use App\Enums\NewsProviderEnum;
use App\Traits\ImportArticles;

class TheGuardianService implements NewsService
{
    use ImportArticles;

    private array $config;

    public ?string $query = null;
    public int $pageNo = 1;
    public int $pageSize = 50;

    public ?string $from = null;
    public ?string $to = null;

    function __construct()
    {
        $this->config = config('news.the_guardian');
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
     * @param int $pageSize max 50
     * @return $this
     */
    public function pageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * Sets the page number.
     * @param int $pageNo
     * @return $this
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
                'api-key' => $this->config['api_key'],
                'from-date' => $this->from,
                'to-date' => $this->to,
                'page' => $this->pageNo,
                'page-size' =>  $this->pageSize,
                'show-fields' => 'headline,bodyText,publication,byline,thumbnail',
                'query-fields' => $this->query ? 'body,headline' : null,
                'q' => $this->query,
            ])->throw()->json('response.results');

            return $this->normalizeData($articles);
        } catch (\Exception $e) {
            Log::error("Error while fetching news from The Guardian: {$e->getMessage()}");
        }
        return [];
    }

    private function normalizeData(array $articles): array
    {
        return array_map(function ($v) {
            return [
                'author' => $v['fields']['byline'] ?? null,
                'source' => $v['fields']['publication'] ?? null,
                'title' => $v['fields']['headline'],
                'content' => $v['fields']['bodyText'] ?? null,
                'category' => $v['sectionName'] ?? null,
                'image_url' => $v['fields']['thumbnail'] ?? null,
                'published_at' => $v['webPublicationDate'] ? Carbon::parse($v['webPublicationDate']) : Carbon::now(),
                'source_url' => $v['webUrl'] ?? null,
            ];
        }, $articles);
    }
}
