<?php

namespace App\Http\Controllers\Api; 

use App\Models\Sheet;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class SheetApiController extends BaseController


{
    protected $model = Sheet::class;
}
