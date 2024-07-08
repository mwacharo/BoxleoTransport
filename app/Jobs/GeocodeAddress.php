<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Log;

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
              $distance = $this->calculateDistance($lat,  $lng);
              Log::info($distance);
                  Log::info($duration);

              $this->order->update([
                  'latitude' => $lat,
                  'longitude' => $lng,
                  'distance' => $distance,
                  'duration' => $duration,
              ]);

              Log::warning($this->order->order_no);

        }
        Log::warning('*****************************************');

      } catch (\Exception $e) {
        Log::error($e);
      }
    }


      public function calculateDistance($lat,  $lng)
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


              $distance = $distanceData['rows'][0]['elements'][0]['distance']['value'] / 1000; // Convert meters to kilometers
              $duration = $distanceData['rows'][0]['elements'][0]['duration']['value'] / 60; // Convert seconds to minutes

            return [$distance, $duration];
          }
          return 0;
        } catch (\Exception $e) {
          Log::error($e);
        }
      }
}
