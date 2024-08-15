<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
// use Log;
use Illuminate\Support\Facades\Log; // Add this line

class GeocodeAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }


    public function handle()
{
    try {
        Log::warning('*****************************************');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $address = $this->order->address;

        $geocodeResponse = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'address' => $address,
            'key' => $apiKey,
            'region' => 'ke'
        ]);

        $geocodeData = $geocodeResponse->json();

        if ($geocodeData['status'] === 'OK') {
            $lat = $geocodeData['results'][0]['geometry']['location']['lat'];
            $lng = $geocodeData['results'][0]['geometry']['location']['lng'];

            Log::warning($lat);
            Log::warning($lng);

            // Calculate distance and duration
            $geo = $this->calculateDistance($lat, $lng);
            if (!empty($geo)) {
                $distance = $geo[0];
                $duration = $geo[1];
                Log::info($distance);
                Log::info($duration);

                // Update order with geocoded data
                $this->order->update([
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'distance' => $distance,
                    'duration' => $duration,
                ]);

                Log::warning($this->order->order_no);
            }
        }
        Log::warning('*****************************************');

    } catch (\Exception $e) {
        Log::error($e);
    }
}

public function calculateDistance($lat, $lng)
{
    try {
        $apiKey = env('GOOGLE_MAPS_API_KEY');

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
        Log::alert($distanceData);

        if ($distanceData['status'] === 'OK') {
            $distanceElement = $distanceData['rows'][0]['elements'][0];

            // Check if 'duration' exists in the response
            if (isset($distanceElement['duration'])) {
                $distance = $distanceElement['distance']['value'] / 1000; // Convert meters to kilometers
                $duration = $distanceElement['duration']['value'] / 60; // Convert seconds to minutes

                return [$distance, $duration];
            } else {
                Log::error("Duration key is missing in the API response");
            }
        }
        return [];
    } catch (\Exception $e) {
        Log::error($e);
        return [];
    }
}


  
}
