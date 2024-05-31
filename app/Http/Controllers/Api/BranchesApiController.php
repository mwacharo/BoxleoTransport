<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchesApiController extends BaseController
{
    protected $model =Branch::class;
}
