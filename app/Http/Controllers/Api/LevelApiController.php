<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Level;

class LevelApiController extends BaseController
{
    protected $model =Level::class;
}
