<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Http\Resources\AuthorResource;

class AuthorController extends Controller
{
    /**
     * Display a listing of the authors.
     */
    public function index()
    {
        return $this->successResponse('Authors fetched successfully', AuthorResource::collection(Author::all()));
    }
}
