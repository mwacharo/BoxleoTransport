<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;

class OrderStatusApiController extends BaseController
{
    //
    protected $model =OrderStatus::class;

}
