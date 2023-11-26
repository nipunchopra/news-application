<?php

namespace App\Jobs;

use App\Enums\NewsProviderEnum;
use App\Services\NewsAPIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncNewsFromNewsAPI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private NewsAPIService $newsAPIService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->newsAPIService = new NewsAPIService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Syncing news from NewsAPI...');

        $articles = $this->newsAPIService->from(now()->yesterday())
            ->to(now())
            ->getArticles();

        if (!empty($articles)) {
            Log::info('Importing articles from NewsAPI...');
            $this->newsAPIService->importArticles($articles, NewsProviderEnum::NEWS_API);
        }

        Log::info('Finished importing articles from NewsAPI.');
    }
}
