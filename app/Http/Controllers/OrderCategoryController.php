<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderCategoryRequest;
use App\Http\Requests\UpdateOrderCategoryRequest;
use App\Models\OrderCategory;

class OrderCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = Auth()->user();
        return view('orders.category',['user'=>$user]);
    }

    
}
