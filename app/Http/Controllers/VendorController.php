<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorevendorRequest;
use App\Http\Requests\UpdatevendorRequest;
use App\Models\vendor;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
            $user = auth()->user();

        return view('vendors.index', ['user' => $user]);
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
    public function store(StorevendorRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatevendorRequest $request, vendor $vendor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(vendor $vendor)
    {
        //
    }
}
