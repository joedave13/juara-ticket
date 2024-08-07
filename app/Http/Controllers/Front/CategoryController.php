<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {
        //
    }

    public function show(Category $category)
    {
        $data = $this->categoryService->getCategoryDetailData($category);

        return view('pages.category.show', [
            'category' => $data
        ]);
    }
}
