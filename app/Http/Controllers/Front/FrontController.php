<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\FrontService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function __construct(protected FrontService $frontService)
    {
        //
    }

    public function index()
    {
        $data = $this->frontService->getFrontPageData();
        dd($data);
    }
}
