<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

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
        $apiKey = config('services.google.maps_api_key');
        $address = $this->order->address;

        $geocodeResponse = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'address' => $address,
            'key' => $apiKey,
        ]);

        $geocodeData = $geocodeResponse->json();

        if ($geocodeData['status'] === 'OK') {
            $lat = $geocodeData['results'][0]['geometry']['location']['lat'];
            $lng = $geocodeData['results'][0]['geometry']['location']['lng'];

            $this->order->update([
                'latitude' => $lat,
                'longitude' => $lng,
            ]);

            // Dispatch the CalculateDistance job
            CalculateDistance::dispatch($this->order);
        }
    }
}
