<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Bin;

class BinApiController extends BaseController
{
  protected $model =Bin::class;

}
