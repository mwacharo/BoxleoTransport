<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Bay;


class BayApiController extends BaseController
{

  protected $model =Bay::class;

}
