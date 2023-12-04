<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lang = request('lang');
        $categories = Category::query()
                ->when($lang == 'ar' ,
                fn($query) =>
                        $query->select('id','name_AR as name')
                ,
                fn($query)=>
                        $query->select('id','name_EN as name')
                )
                ->get();

        return $this->sendResponse($categories , 'categories');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }
}
