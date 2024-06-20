<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Warehouse;



class WarehouseApiController extends BaseController
{
  protected $model =Warehouse::class;

}
