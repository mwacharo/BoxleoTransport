<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Rider;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
class RiderApiController extends BaseController
{
    protected $model =Rider::class;


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



  public function podstaticts()
  {
      // Fetch riders with the count of their assigned orders and the status of PODs
      $riders = Rider::withCount(['orders'])
          ->with(['orders' => function($query) {
              $query->select('id', 'rider_id', 'pod_status');
          }])
          ->get()
          ->map(function ($rider) {
              $podStatuses = $rider->orders->pluck('pod_status');
              $rider->pod_status = $podStatuses->contains('No') ? 'Pending' : 'Yes';
              $rider->status = $rider->pod_status === 'Yes' ? 'Cleared' : 'Pending';
              return $rider;
          });

      return response()->json($riders);
  }

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
