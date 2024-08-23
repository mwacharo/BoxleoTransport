<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index()
    {
        //
        $user = auth()->user();
        // return 0;

        return view('orders.return', ['user' => $user]);
        
    }
    
}
