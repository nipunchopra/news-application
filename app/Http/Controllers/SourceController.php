<?php

namespace App\Http\Controllers;

use App\Http\Resources\SourceResource;
use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    /**
     * Display a listing of the sources.
     */
    public function index()
    {
        return $this->successResponse('Sources fetched successfully', SourceResource::collection(Source::all()));
    }
}
