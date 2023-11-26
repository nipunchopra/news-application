<?php

namespace App\Traits;

use App\Enums\NewsProviderEnum;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait ImportArticles
{
    /**
     * Import articles from an array of articles.
     * @param array $articles
     * @return bool
     */
    public function importArticles(array $articles, NewsProviderEnum $newsProvider): bool
    {
        if (empty($articles)) return true;

        try {
            $latestArticleSlug = Article::select('slug')->where('news_provider', $newsProvider->value)->latest()->first()?->slug;

            foreach ($articles as $article) {

                try {
                    if (($articleSlug = Str::slug($article['title'])) === $latestArticleSlug) break;

                    $source = $category = $author = null;

                    if (!empty($article['source'])) {
                        $source = Source::firstOrCreate(
                            ['slug' => Str::slug($article['source'])],
                            ['name' => $article['source']],
                        );
                    }

                    if (!empty($article['category'])) {
                        $category = Category::firstOrCreate(
                            ['slug' => Str::slug($article['category'])],
                            ['name' => $article['category']],
                        );
                    }

                    // Issue with NewsAPI sometimes returning a long string with HTML code for author
                    if (!empty($article['author']) && strlen($article['author']) < 100) {
                        $author = Author::firstOrCreate(
                            ['slug' => Str::slug($article['author'])],
                            ['name' => $article['author']],
                        );
                    }

                    Article::updateOrCreate(
                        ['slug' => $articleSlug],
                        [
                            'title' => $article['title'],
                            'author_id' => $author?->id,
                            'category_id' => $category?->id,
                            'source_id' => $source?->id,
                            'source_url' => $article['source_url'],
                            'image_url' => $article['image_url'],
                            'content' => $article['content'],
                            'description' => $article['description'],
                            'news_provider' => $newsProvider->value,
                            'published_at' => ($article['published_at'])?->toDateTimeString()
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error("Error while inserting articles into database: {$e->getMessage()}");
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error while importing articles: {$e->getMessage()}");
        }
        return false;
    }
}
