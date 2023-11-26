<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        return $this->successResponse('Categories fetched successfully', CategoryResource::collection(Category::all()));
    }
}
