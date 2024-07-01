<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Vehicle;

class VehicleApiController extends BaseController
{
  protected $model =Vehicle::class;

}
