<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }



    public function createBill(Request $request)
    {
        $merchantId = $request->merchant_id;
        $services = $request->services; // List of services with details like weight, distance, etc.
        
        $total = 0;
        
        foreach ($services as $service) {
            $serviceType = $service['type']; // E.g., 'WEIGHT', 'DISTANCE', etc.
            $serviceData = $service['data']; // E.g., actual weight, distance traveled, etc.
            
            // Fetch conditions for this service
            $conditions = Condition::where('condition_type', $serviceType)->get();
            
            foreach ($conditions as $condition) {
                // Apply condition logic (weight, distance, percentage, etc.)
                if ($serviceType === 'WEIGHT') {
                    $total += $this->calculateWeight($serviceData, $condition);
                } elseif ($serviceType === 'DISTANCE') {
                    $total += $this->calculateDistance($serviceData, $condition);
                }
                // Add similar checks for other services (PICKUP-FEES, STORAGE, etc.)
            }
        }
        
        // Save transaction and return total
        $transaction = Transaction::create([
            'merchant_id' => $merchantId,
            'amount' => $total,
        ]);
        
        return response()->json(['total' => $total, 'transaction_id' => $transaction->id]);
    }
    
    public function calculateWeight($weight, $condition)
    {
        // Example logic for weight-based billing
        if ($weight <= 5) {
            return $condition->amount;
        } else {
            return ($weight - 5) * $condition->extra_charge_per_kg;
        }
    }
    
    public function calculateDistance($distance, $condition)
    {
        // Example logic for distance-based billing
        if ($distance <= 10) {
            return $condition->amount;
        } else {
            return ($distance - 10) * $condition->extra_charge_per_km;
        }
    }
    
    public function getBill($id)
    {
        $transaction = Transaction::find($id);
        return response()->json($transaction);
    }
}
