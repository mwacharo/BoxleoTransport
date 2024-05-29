<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarketRequest;
use App\Http\Requests\UpdateMarketRequest;
use App\Models\Market;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = auth()->user();

        return view('market.index', ['user' => $user]);
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
    public function store(StoreMarketRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Market $market)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Market $market)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketRequest $request, Market $market)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Market $market)
    {
        //
    }
}
