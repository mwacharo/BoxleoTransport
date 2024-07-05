<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\Fleet;

class FleetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user= Auth()->user();
        return view('fleet.index',['user'=>$user]);
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
    public function store(StoreFleetControllerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FleetController $fleetController)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FleetController $fleetController)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFleetControllerRequest $request, FleetController $fleetController)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FleetController $fleetController)
    {
        //
    }
}
