<?php

namespace App\Models;

use App\Enums\NewsProviderEnum;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $casts = [
        'published_at' => 'datetime',
        'news_provider' => NewsProviderEnum::class,
    ];

    protected $fillable = [
        'title',
        'slug',
        'author_id',
        'category_id',
        'source_id',
        'source_url',
        'image_url',
        'content',
        'description',
        'news_provider',
        'published_at',
    ];

    /**
     * This function returns the author associated with the article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    /**
     * This function returns the category associated with the article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * This function returns the source associated with the article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }
}
