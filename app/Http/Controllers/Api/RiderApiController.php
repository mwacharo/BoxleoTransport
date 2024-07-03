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

}
