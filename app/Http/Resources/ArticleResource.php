<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source' => $this->source?->name,
            'author' => $this->author?->name,
            'category' => $this->category?->name,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'source_url' => $this->source_url,
            'image_url' => $this->image_url,
            'published_at' => $this->published_at->format('d-m-Y H:i A'),
            'content' => $this->content,
        ];
    }
}
