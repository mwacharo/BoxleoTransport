<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClearanceController extends Controller
{
    public function index()
    {
        $user = auth()->user()->id;
        // return 0;

        return view('orders.clearance',['user' => $user]);
    }
    
}
