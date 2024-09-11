<?php

namespace App\Http\Controllers\Api;

use App\Events\RIderCreated;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

class RiderApiController extends BaseController
{
    protected $model =Rider::class;


    public function store(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'name' => 'required|max:255',
        'email' => 'required|email|max:255|unique:riders,email',
        'address' => 'required|max:255',
        'phone' => 'required|max:255',
        'branch_id' => 'required|max:255',
        'geofence_id' => 'required|max:255',
    ]);

    // Generate a random password
    $password = Str::random(8);
    $validatedData['password'] = Hash::make($password);

    $name= $validatedData['name'];
    $email = $validatedData['email'];

    // Create the rider
    $rider = Rider::create($validatedData);

    // Log the creation
    Log::info('New rider created:', $validatedData);

    // Dispatch the RiderCreated event
    event(new RiderCreated($rider, $password));

    // Send a welcome email
    Mail::to($rider->email)->send(new WelcomeEmail($rider, $password ,$name, $email));

    // Return a response
    return response()->json(['message' => 'Rider created successfully', 'data' => $rider], 201);
}
    

    // $password = Str::random(8);
    // $validatedData['password'] = Hash::make($password);



       // The index method that calls the private podStatistics method
       public function index()
       {
           // Call the private function to fetch riders with POD statistics
           $ridersWithStatistics = $this->podStatistics();
   
           // Return the response
           return response()->json($ridersWithStatistics, 200);
       }
   
       // Private function to fetch riders with POD statistics
    //    private function podStatistics()
    //    {
    //        // Fetch riders with the count of their assigned orders
    //        // Also fetch the status of PODs (Proof of Delivery) for each order
    //        $riders = Rider::withCount(['orders'])
    //            ->with(['orders' => function($query) {
    //                $query->select('id', 'rider_id', 'pod');
    //            }])
    //            ->get()
    //            ->map(function ($rider) {
    //                // Extract POD statuses for the rider's orders
    //                $podStatuses = $rider->orders->pluck('pod_status');
   
    //                // Determine if there are any pending PODs
    //                $rider->pod_status = $podStatuses->contains('No') ? 'Pending' : 'Yes';
   
    //                // Set rider status based on POD status
    //                $rider->status = $rider->pod_status === 'Yes' ? 'Cleared' : 'Pending';
   
    //                return $rider;
    //            });
   
    //        return $riders;
    //    }


    private function podStatistics()
{
    // Fetch riders with the count of their assigned orders
    // Also fetch the status of PODs (Proof of Delivery) for each order
    $riders = Rider::withCount(['orders'])
        ->with(['orders' => function($query) {
            $query->select('id', 'rider_id', 'pod');
        }])
        ->get()
        ->map(function ($rider) {
            // Extract POD statuses for the rider's orders
            $podStatuses = $rider->orders->pluck('pod');

            // Count the number of orders with and without POD
            $rider->pod_count = $podStatuses->filter(function ($value) {
                return $value === 'Yes';
            })->count();

            $rider->no_pod_count = $podStatuses->filter(function ($value) {
                return $value !== 'Yes';
                        })->count();

            // Determine if there are any pending PODs
            $rider->pod_status = $rider->no_pod_count > 0 ? 'Pending' : 'Yes';

            // Set rider status based on POD status
            $rider->status = $rider->pod_status === 'Yes' ? 'Cleared' : 'Pending';

            return $rider;
        });

    return $riders;
}



    // public function index()
    // {
    //     return response()->json($this->model::all(), 200);
    // }


    public function updateGeofence(Request $request, $id)
  {
      // Validate the incoming request
      $validator = Validator::make($request->all(), [
          'geofence_id' => 'required|exists:geofences,id',
      ]);

      if ($validator->fails()) {
          return response()->json(['message' => 'Validation Error.', 'errors' => $validator->errors()], 400);
      }

      // Find the rider
      $rider = Rider::find($id);

      if (!$rider) {
          return response()->json(['message' => 'Rider not found.'], 404);
      }

      // Update the geofence_id
      $rider->geofence_id = $request->geofence_id;
      $rider->save();

      return response()->json(['message' => 'Rider geofence updated successfully', 'rider' => $rider], 201);
  }



//   public function podstaticts()
//   {
//       // Fetch riders with the count of their assigned orders and the status of PODs
//       $riders = Rider::withCount(['orders'])
//           ->with(['orders' => function($query) {
//               $query->select('id', 'rider_id', 'pod_status');
//           }])
//           ->get()
//           ->map(function ($rider) {
//               $podStatuses = $rider->orders->pluck('pod_status');
//               $rider->pod_status = $podStatuses->contains('No') ? 'Pending' : 'Yes';
//               $rider->status = $rider->pod_status === 'Yes' ? 'Cleared' : 'Pending';
//               return $rider;
//           });

//       return response()->json($riders);
//   }

  public function clear(Rider $rider)
  {
      // Check if all orders have POD status as 'Yes'
      $allCleared = $rider->orders()->where('pod_status', 'No')->doesntExist();

      if ($allCleared) {
          $rider->update(['status' => 'Cleared']);
          return response()->json(['message' => 'Rider cleared successfully.']);
      } else {
          return response()->json(['message' => 'Rider cannot be cleared. Not all PODs are marked as Yes.'], 400);
      }
  }


//   public function updateStatus(Request $request, $id)
//   {
//       // Validate the incoming request
//       $request->validate([
//           'status' => 'required|string',
//           'comments' => 'nullable|string',
//           // add more fields if needed
//       ]);

//       // Find the rider by ID
//       $rider = Rider::findOrFail($id);

//       // Update the rider's status and any other fields
//       $rider->status = $request->input('status');
//       $rider->comments = $request->input('comments'); // Assuming there's a comments field
//       $rider->save();

//       // Return a success response
//       return response()->json(['message' => 'Rider status updated successfully'], 200);
//   }

}
