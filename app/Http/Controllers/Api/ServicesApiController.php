<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Condition;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServicesApiController extends Controller
{



    // Method to fetch all services with their conditions
    //  public function getServicesWithConditions()
    //  {
    //      // Fetch services with their conditions using eager loading
    //      $services = Service::with('conditions')->get();
    //     //  $services = Service::with('conditions')->get();


    //      return response()->json($services);
    //  }


    // Method to fetch all services with their conditions
    // public function getServicesWithConditions($vendorid)
    // {
    //     // Fetch services with their conditions using eager loading
    //     $services = Service::with('conditions')
    //         ->where('vendor_id', $vendorid)
    //         ->get();

    //     return response()->json($services);
    // }



    public function getServicesWithConditions($vendorid)
{
    // Fetch services with their conditions using eager loading
    $services = Service::with(['conditions' => function ($query) use ($vendorid) {
        $query->where('vendor_id', '=', $vendorid);
    }])
        ->get();

    return response()->json($services);
}



public function storeConditions(Request $request)
{
    $validated = $request->validate([
        'services.*.conditions.*.vendor_id' => 'required|numeric',

        'services' => 'required|array',
        'services.*.id' => 'required|exists:services,id',
        'services.*.conditions' => 'required|array',
        'services.*.conditions.*.rate_3t' => 'nullable|string',
        'services.*.conditions.*.rate_5t' => 'nullable|string',
        'services.*.conditions.*.rate_7t' => 'nullable|string',
        'services.*.conditions.*.rate_10t' => 'nullable|string',
        'services.*.conditions.*.condition_amount' => 'nullable|string',
        'services.*.conditions.*.condition_percentage' => 'nullable|string',
        'services.*.conditions.*.region' => 'nullable|string',
        'services.*.conditions.*.route' => 'nullable|string',
    ]);


    // dd($validated);

    DB::beginTransaction();

    try {
        foreach ($validated['services'] as $serviceData) {
            $service = Service::find($serviceData['id']);

            // dd($service);

            // Iterate over each condition for the service
            foreach ($serviceData['conditions'] as $conditionData) {
                // Define the condition attributes to match (vendor_id, service_id, and optional region/route)
                $conditionAttributes = [
                    'vendor_id' => $conditionData['vendor_id'],
                    'service_id' => $serviceData['id'],
                    'region' => $conditionData['region'],
                    'route' => $conditionData['route'],
                ];
            
                // Define the fields that will be updated or inserted
                $conditionValues = [
                    'condition_amount' => $conditionData['condition_amount'],
                    'condition_percentage' => $conditionData['condition_percentage'],
                    'rate_3t' => $conditionData['rate_3t'],
                    'rate_5t' => $conditionData['rate_5t'],
                    'rate_7t' => $conditionData['rate_7t'],
                    'rate_10t' => $conditionData['rate_10t'],
                    'vendor_id' => $conditionData['vendor_id'],
                    'service_id' => $serviceData['id'],
                    'region' => $conditionData['region'],
                    'route' => $conditionData['route'],
                ];
            
                // Debugging - Log condition attributes and values before updating/creating
                Log::info('Attempting to update or create condition', [
                    'attributes' => $conditionAttributes,
                    'values' => $conditionValues,
                ]);
            
                try {
                    // Use updateOrCreate to update if the condition exists, or create a new one if not
                    // $condition = Condition::Create($conditionAttributes, $conditionValues);

                    $conditionValues->save(); // Save the unique condition

            
                    // Debugging - Log the condition after updating/creating
                    // Log::info('Condition update or create successful', [
                    //     'condition' => $condition->toArray()
                    // ]);
            
                } catch (\Exception $e) {
                    // Debugging - Log the error if something goes wrong
                    Log::error('Failed to update or create condition', [
                        'error' => $e->getMessage(),
                        'attributes' => $conditionAttributes,
                        'values' => $conditionValues,
                    ]);
                    throw $e;  // Optionally, you can rethrow the exception to handle it further up.
                }
            }
            
        }

        DB::commit();
        return response()->json(['message' => 'Conditions stored successfully'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to store conditions', 'details' => $e->getMessage()], 500);
    }
}



// public function storeConditions(Request $request)
// {
//     $validated = $request->validate([
//         'services.*.conditions.*.vendor_id' => 'required|numeric',

//         'services' => 'required|array',
//         'services.*.id' => 'required|exists:services,id',
//         'services.*.conditions' => 'required|array',
//         'services.*.conditions.*.rate_3t' => 'nullable|string',
//         'services.*.conditions.*.rate_5t' => 'nullable|string',
//         'services.*.conditions.*.rate_7t' => 'nullable|string',
//         'services.*.conditions.*.rate_10t' => 'nullable|string',
//         'services.*.conditions.*.condition_amount' => 'nullable|string',
//         'services.*.conditions.*.condition_percentage' => 'nullable|string',
//         'services.*.conditions.*.region' => 'nullable|string',
//         'services.*.conditions.*.route' => 'nullable|string',
//     ]);

//     DB::beginTransaction();

//     try {
//         foreach ($validated['services'] as $serviceData) {
//             $service = Service::find($serviceData['id']);

//             // Group conditions to avoid duplicates
//             $uniqueConditions = collect($serviceData['conditions'])->unique(function ($conditionData) {
//                 return $conditionData['vendor_id'] . 
//                        $conditionData['condition_amount'] . 
//                        $conditionData['condition_percentage'] . 
//                        $conditionData['rate_3t'] . 
//                        $conditionData['rate_5t'] . 
//                        $conditionData['rate_7t'] . 
//                        $conditionData['rate_10t'] . 
//                        $conditionData['region'] . 
//                        $conditionData['route'];
//             });

//             // Iterate over each unique condition for the service
//             foreach ($uniqueConditions as $conditionData) {
//                 $condition = new Condition();
//                 $condition->service_id = $service->id;
//                 $condition->condition_amount = $conditionData['condition_amount'];
//                 $condition->condition_percentage = $conditionData['condition_percentage'];
//                 $condition->rate_3t = $conditionData['rate_3t'];
//                 $condition->rate_5t = $conditionData['rate_5t'];
//                 $condition->rate_7t = $conditionData['rate_7t'];
//                 $condition->rate_10t = $conditionData['rate_10t'];
//                 $condition->vendor_id = $conditionData['vendor_id'];
//                 $condition->region = $conditionData['region'];
//                 $condition->route = $conditionData['route'];

//                 $condition->save(); // Save the unique condition
//             }
//         }

//         DB::commit();
//         return response()->json(['message' => 'Conditions stored successfully'], 200);
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return response()->json(['error' => 'Failed to store conditions', 'details' => $e->getMessage()], 500);
//     }
// }




// public function storeConditions(Request $request)
// {
//     $validated = $request->validate([
//         'services.*.conditions.*.vendor_id' => 'required|numeric',

//         'services' => 'required|array',
//         'services.*.id' => 'required|exists:services,id',
//         'services.*.conditions' => 'required|array',
//         'services.*.conditions.*.rate_3t' => 'nullable|string',
//         'services.*.conditions.*.rate_5t' => 'nullable|string',
//         'services.*.conditions.*.rate_7t' => 'nullable|string',
//         'services.*.conditions.*.rate_10t' => 'nullable|string',
//         'services.*.conditions.*.condition_amount' => 'nullable|string',
//         'services.*.conditions.*.condition_percentage' => 'nullable|string',
//         'services.*.conditions.*.region' => 'nullable|string',
//         'services.*.conditions.*.route' => 'nullable|string',

        
//     ]);

//     // dd($validated);

//     DB::beginTransaction();

//     try {
//         foreach ($validated['services'] as $serviceData) {
//             $service = Service::find($serviceData['id']);

//             // Iterate over each condition for the service
//             foreach ($serviceData['conditions'] as $conditionData) {
//                 $condition = new Condition();
//                 $condition->service_id = $service->id;
//                 $condition->condition_amount = $conditionData['condition_amount'];
//                 $condition->condition_percentage = $conditionData['condition_percentage'];
//                 $condition->rate_3t = $conditionData['rate_3t'];
//                 $condition->rate_5t = $conditionData['rate_5t'];
//                 $condition->rate_7t = $conditionData['rate_7t'];
//                 $condition->rate_10t = $conditionData['rate_10t'];
//                 $condition->vendor_id = $conditionData['vendor_id'];
//                 $condition->region = $conditionData['region'];
//                 $condition->route = $conditionData['route'];



//                 $condition->save(); // Save the condition
//             }
//         }

//         DB::commit();
//         return response()->json(['message' => 'Conditions stored successfully'], 200);
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return response()->json(['error' => 'Failed to store conditions', 'details' => $e->getMessage()], 500);
//     }
// }


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


