<?php

namespace App\Jobs;

use App\Enums\NewsProviderEnum;
use App\Services\TheGuardianService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncNewsFromTheGuardian implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private TheGuardianService $theGuardianService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->theGuardianService = new TheGuardianService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Syncing news from The Guardian...');

        $articles = $this->theGuardianService->from(now()->yesterday())
            ->to(now())
            ->getArticles();

        if (!empty($articles)) {
            Log::info('Importing articles from The Guardian...');
            $this->theGuardianService->importArticles($articles, NewsProviderEnum::THE_GUARDIAN);
        }

        Log::info('Finished importing articles from The Guardian.');
    }
}
