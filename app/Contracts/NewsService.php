<?php

namespace App\Contracts;

use Carbon\Carbon;

interface NewsService
{
    public function from(Carbon $from): self;
    public function to(Carbon $to): self;

    public function search(string $query): self;
    public function pageNo(int $pageNo): self;
    public function pageSize(int $pageSize): self;

    public function getArticles(): array;
}
