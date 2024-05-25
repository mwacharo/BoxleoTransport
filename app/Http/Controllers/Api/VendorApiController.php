<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\Vue;

class VendorApiController extends BaseController
{
    //
    protected $model =Vendor::class;
}
