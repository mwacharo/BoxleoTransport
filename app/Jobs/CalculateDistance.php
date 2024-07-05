<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class CalculateDistance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        $apiKey = config('services.google.maps_api_key');
        $lat = $this->order->latitude;
        $lng = $this->order->longitude;

        if (!$lat || !$lng) {
            return; // Skip if coordinates are not available
        }

        $storeLatitude = -1.3074432;
        $storeLongitude = 36.9033216; 

        $distanceResponse = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
            'origins' => "{$storeLatitude},{$storeLongitude}",
            'destinations' => "{$lat},{$lng}",
            'key' => $apiKey,
        ]);

        $distanceData = $distanceResponse->json();

        if ($distanceData['status'] === 'OK') {
            $distance = $distanceData['rows'][0]['elements'][0]['distance']['value'] / 1000; // Convert meters to kilometers
            $this->order->update(['distance' => $distance]);
        }
    }
}
