<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;


use App\Models\Geofence;

class GeofenceApiController extends BaseController
{
    //
      protected $model =Geofence::class;


        public function index()
        {
            $geofences = Geofence::all();

            return response()->json($geofences);
        }

    public function store(Request $request)
   {
       $data = $request->validate([
           'path' => 'required|array',
           'path.*.lat' => 'required|numeric',
           'path.*.lng' => 'required|numeric',
       ]);

       $geofence = Geofence::create($data);

       return response()->json(['message' => 'Geofence saved successfully', 'geofence' => $geofence], 201);
   }
}
