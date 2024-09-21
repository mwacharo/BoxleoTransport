<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServicesApiController extends Controller
{



     // Method to fetch all services with their conditions
     public function getServicesWithConditions()
     {
         // Fetch services with their conditions using eager loading
         $services = Service::with('conditions')->get();
        //  $services = Service::with('conditions')->get();

 
         return response()->json($services);
     }


    public function index()
    {

        $services = Service::all();

        return response()->json([
            'services' => $services,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $service = Service::create($validatedData);

        return response()->json([
            'message' => 'Service created successfully',
            'data' => $service,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found',
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'string|max:255',
        ]);

        $service->update($validatedData);

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => $service,
        ]);
    }

    public function destroy(string $id)
    {

        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->delete();
        return response()->json(['message' => 'Service deleted successfully'], 204);
    }

    

}