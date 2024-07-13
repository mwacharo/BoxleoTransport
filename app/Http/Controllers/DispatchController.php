<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDispatchRequest;
use App\Http\Requests\UpdateDispatchRequest;
use App\Models\Dispatch;

class DispatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 
        $user= Auth()->user();
        return view('orders.dispatch',['user'=>$user]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDispatchRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dispatch $dispatch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dispatch $dispatch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDispatchRequest $request, Dispatch $dispatch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dispatch $dispatch)
    {
        //
    }
}
