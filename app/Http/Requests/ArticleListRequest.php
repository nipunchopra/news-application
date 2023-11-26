<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',

            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',

            'author_ids' => 'nullable|array',
            'author_ids.*' => 'integer|exists:authors,id',

            'source_ids' => 'nullable|array',
            'source_ids.*' => 'integer|exists:sources,id',

            'search' => 'nullable|string',
            'order_by' => 'nullable|string|in:published_at,created_at',
            'order' => 'required_with:order_by|string|in:asc,desc',
        ];
    }
}
