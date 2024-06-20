<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Http\Controllers\BaseController;


class AreaApiController extends BaseController
{
    protected $model =Area::class;

}
