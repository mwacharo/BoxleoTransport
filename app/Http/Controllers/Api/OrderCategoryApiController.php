<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\OrderCategory;
use Illuminate\Http\Request;

class OrderCategoryApiController extends BaseController
{
    //
    protected $model =OrderCategory::class;

}
