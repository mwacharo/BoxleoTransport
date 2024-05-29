<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends BaseController
{
    /**
     * Display a listing of the resource.
     */


     protected $model =Order::class;

    public function index()
    {
        // 
        $orders= Order::with('vendor')->get();
        return  response()->json($orders);
    }

  
}
