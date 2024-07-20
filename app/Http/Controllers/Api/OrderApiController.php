<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\GeocodingService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProductInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderApiController extends BaseController
{
    /**
     * Display a listing of the resource.
     */


    protected $model = Order::class, $geocodingService;



    //   public function geocodeAddress(Request $request)
    // {
    //     $orderId = $request->input('orderId');
    //     $order = Order::findOrFail($orderId);
    //
    //
    //
    //     if ($order->latitude && $order->longitude) {
    //         return response()->json([
    //             'longitude' => $order->latitude,
    //             'longitude' => $order->longitude,
    //         ]);
    //     }
    //
    //
    //     $address = $order->address; // Assuming you have an address field
    //     $apiKey = config('services.google.maps_api_key');
    //
    //     dd($apiKey);
    //
    //     $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
    //         'address' => $address,
    //         'key' => $apiKey,
    //     ]);
    //
    //     $data = $response->json();
    //
    //     if ($data['status'] === 'OK') {
    //         $location = $data['results'][0]['geometry']['location'];
    //         $order->update([
    //             'lat' => $location['lat'],
    //             'lng' => $location['lng'],
    //         ]);
    //
    //         return response()->json($location);
    //     }
    //
    //     return response()->json(['error' => 'Geocoding failed'], 400);
    // }


    public function assignOrders(Request $request)
    {
        $orderIds = $request->input('order_ids');
        $agentId = $request->input('agent_id');

        if (empty($orderIds) || empty($agentId)) {
            return response()->json([
                'message' => 'Order IDs or Agent ID not provided'
            ], 400);
        }

        // Update the orders with the agent ID
        Order::whereIn('id', $orderIds)->update(['agent_id' => $agentId]);

        return response()->json([
            'message' => 'Orders assigned successfully'
        ]);
    }


    public function geocodeAddress(Request $request)
    {
        $orderId = $request->input('orderId');
        $order = Order::findOrFail($orderId);

        if ($order->lat && $order->lng) {
            return response()->json([
                'lat' => $order->lat,
                'lng' => $order->lng,
            ]);
        }

        $address = $order->address; // Assuming you have an address field
        $apiKey = config('services.google_maps.api_key');
        // dd($apiKey);

        if (!$apiKey) {
            Log::error('Google Maps API key is not set');
            return response()->json(['error' => 'API key configuration error'], 500);
        }

        Log::info('Geocoding address: ' . $address);
        Log::info('Using API key: ' . substr($apiKey, 0, 5) . '...');  // Log first 5 characters for debugging

        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'address' => $address,
            'key' => $apiKey,
        ]);

        $data = $response->json();
        Log::info('Geocoding response: ' . json_encode($data));

        if ($data['status'] === 'OK') {
            $location = $data['results'][0]['geometry']['location'];
            $order->update([
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
            ]);
            return response()->json($location);
        }

        Log::error('Geocoding failed: ' . $data['status']);
        return response()->json(['error' => 'Geocoding failed: ' . $data['status']], 400);
    }

    public function index()
    {
        $orders = Order::with([
            'vendor.products',
            'products.productInstances',
            'orderProducts.product',
            'orderProducts.productInstances'
        ])->get();

        return response()->json($orders);
    }


    // public function index()
    // {
    //     $orders = Order::with([
    //         'orderProducts.productInstances.product',
    //         'products',
    //         'vendor',
    //         'orderProducts.product'
    //     ])->get();

    //     return response()->json($orders);
    // }


    // public function index()
    // {
    //     // Fetch all orders with their related order products, products, vendor, and order product instances
    //     $orders = Order::with([
    //         'orderProducts',
    //         'orderProducts.productInstances.product',
    //         'products',
    //         'vendor'
    //     ])->get();

    //     return response()->json($orders);
    // }


    // public function index()
    // {
    //     //
    //     $orders = Order::with('orderProducts','products','vendor')->get();
    //     return  response()->json($orders);
    // }


    // public function index()
    // {
    //     $orders = Order::with('products')->get();

    //     $orders = $orders->map(function ($order) {
    //         $order->product_names = $order->products->pluck('name');
    //         return $order;
    //     });

    //     return response()->json($orders);
    // }



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

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    public function details(Request $request)
    {

        $ids = $request->input('orderIds');

        if (empty($ids)) {
            return response()->json([
                'message' => 'No IDs provided'
            ], 400);
        }

        $orders = Order::whereIn('id', $ids)->get();

        return response()->json($orders);
    }


    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        // Validate the IDs if needed
        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        // Perform the delete operation
        Order::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Orders deleted successfully']);
    }

    public function bulkAssignRider(Request $request)
    {
        $ids = $request->input('ids', []);
        $riderId = $request->input('rider');
        // dd($riderId);

        if (empty($ids) || !$riderId) {
            return response()->json(['message' => 'Invalid input'], 400);
        }

        Order::whereIn('id', $ids)->update(['rider_id' => $riderId]);

        return response()->json(['message' => 'Rider assigned successfully']);
    }

    public function bulkAssignDriver(Request $request)
    {
        $ids = $request->input('ids', []);
        $driverId = $request->input('driver_id');

        if (empty($ids) || !$driverId) {
            return response()->json(['message' => 'Invalid input'], 400);
        }

        Order::whereIn('id', $ids)->update(['driver_id' => $driverId]);

        return response()->json(['message' => 'Driver assigned successfully']);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $ids = $request->input('ids', []);
        $status = $request->input('status');

        if (empty($ids) || !$status) {
            return response()->json(['message' => 'Invalid input'], 400);
        }

        Order::whereIn('id', $ids)->update(['status' => $status]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function bulkCategorize(Request $request)
    {
        $ids = $request->input('ids', []);
        $category = $request->input('category');

        if (empty($ids) || !$category) {
            return response()->json(['message' => 'Invalid input'], 400);
        }

        Order::whereIn('id', $ids)->update(['order_type' => $category]);

        return response()->json(['message' => 'Orders categorized successfully']);
    }


    public function bulkAutoAllocate(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        // Implement the logic for auto-allocating orders based on geocoding
        // This is a placeholder for the actual implementation
        foreach ($ids as $id) {
            $order = Order::find($id);
            if ($order) {
                // Example logic to assign rider/driver based on geocoding
                $order->update(['rider_id' => $this->allocateRiderBasedOnGeocode($order)]);
            }
        }

        return response()->json(['message' => 'Orders auto-allocated successfully']);
    }

    protected function allocateRiderBasedOnGeocode($order)
    {
        // Implement the actual geocoding and allocation logic here
        // For example, find the nearest available rider/driver based on the order's location
        return 1; // This is a placeholder. Return the actual rider/driver ID.
    }

    public function bulkPrint(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        $orders = Order::whereIn('id', $ids)->get();

        // Generate a PDF or any other format for the orders
        // This is a placeholder for the actual PDF generation logic
        $pdfContent = $this->generatePdf($orders);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="orders.pdf"');
    }

    protected function generatePdf($orders)
    {
        // Implement the actual PDF generation logic here
        // For example, use a library like Dompdf or TCPDF
        return 'PDF content'; // This is a placeholder. Return the actual PDF content.
    }

    public function order($id)
    {
        $order = Order::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => [
                'order' => $order
            ]
        ], 200);
    }


    public function dispatchOrder($orderId)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($orderId);

            foreach ($order->orderProducts as $orderProduct) {
                for ($i = 0; $i < $orderProduct->quantity; $i++) {
                    // $uniqueIdentifier = $this->generateUniqueIdentifier();

                    OrderProductInstance::create([
                        'order_id' => $order->id,
                        'product_id' => $orderProduct->product_id,
                        'product_instance_id' => $orderProduct->id, // Assuming the product instance ID is the same as the order product ID.
                        // 'unique_identifier' => $uniqueIdentifier,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error dispatching order: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
