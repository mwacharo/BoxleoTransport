<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Row;
use App\Http\Controllers\BaseController;



class RowApiController extends BaseController
{
  protected $model =Row::class;

}
