<?php

namespace App\Jobs;

use App\Enums\NewsProviderEnum;
use App\Services\NewYorkTimesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncNewsFromNewYorkTimes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private NewYorkTimesService $newYorkTimesService;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->newYorkTimesService = new NewYorkTimesService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Syncing news from New York Times...');

        $articles = $this->newYorkTimesService->from(now()->yesterday())
            ->to(now())
            ->getArticles();

        if (!empty($articles)) {
            Log::info('Importing articles from New York Times...');
            $this->newYorkTimesService->importArticles($articles, NewsProviderEnum::NEW_YORK_TIMES);
        }

        Log::info('Finished importing articles from New York Times.');
    }
}
