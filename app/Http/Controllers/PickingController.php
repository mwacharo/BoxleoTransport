<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PickingController extends Controller
{
    public function index()
    {
        // 
        $user= Auth()->user();
        return view('orders.picking',['user'=>$user]);
    }
}
