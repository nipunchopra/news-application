<?php

namespace App\Http\Controllers;

use App\Enums\NewsProviderEnum;
use App\Http\Requests\ArticleListRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Jobs\SyncNewsFromNewsAPI;
use App\Models\Article;
use App\Services\NewsAPIService;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Log;


class ArticleController extends Controller
{
    /**
     * Display a listing of the article.
     */
    public function index(ArticleListRequest $request)
    {
        $article = Article::when($request->has('category_ids'), function ($query) use ($request) {
            $query->whereIn('category_id', $request->category_ids);
        })
            ->when($request->has('source_ids'), function ($query) use ($request) {
                $query->whereIn('source_id', $request->source_ids);
            })
            ->when($request->has('author_ids'), function ($query) use ($request) {
                $query->whereIn('author_id', $request->author_ids);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $query->whereFullText(['title', 'content', 'description'], $request->search);
            })
            ->when(!empty($request->from), function ($query) use ($request) {
                $query->whereDate('published_at', '>=', $request->from);
            })
            ->when(!empty($request->has('to')), function ($query) use ($request) {
                $query->whereDate('published_at', '<=', $request->to);
            })
            ->when($request->has('order_by'), function ($query) use ($request) {
                $query->orderBy($request->order_by, $request->order);
            })
            ->when(!$request->has('order_by'), function ($query) use ($request) {
                $query->latest();
            });

        return (new ArticleCollection($article->paginate(10)));
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        return $this->successResponse('Article fetched successfully', new ArticleResource($article));
    }
}
