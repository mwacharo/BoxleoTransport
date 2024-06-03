<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\GeocodingService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderApiController extends BaseController
{
    /**
     * Display a listing of the resource.
     */

     
     protected $model =Order::class ,$geocodingService;


    public function index()
    {
        // 
        $orders= Order::with('vendor')->get();
        return  response()->json($orders);
    }



    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function geocodeOrders()
    {
        $orders = Order::all();
        foreach ($orders as $order) {
            $location = $this->geocodingService->geocode($order->address);
            if ($location) {
                $order->latitude = $location['lat'];
                $order->longitude = $location['lng'];
                $order->save();
            }
        }
        // dd($orders);
        return response()->json($orders);
    }
}
