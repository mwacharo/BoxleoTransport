<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\GeocodingService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPod;
use App\Models\OrderProduct;
use App\Models\OrderProductInstance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


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
            'pods'
            // 'orderProducts.productInstances'
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

    // public function bulkPrint(Request $request)
    // {
    //     $ids = $request->input('ids', []);

    //     if (empty($ids)) {
    //         return response()->json(['message' => 'No IDs provided'], 400);
    //     }

    //     $orders = Order::whereIn('id', $ids)->get();

    //     // Generate a PDF or any other format for the orders
    //     // This is a placeholder for the actual PDF generation logic
    //     $pdfContent = $this->generatePdf($orders);

    //     return response($pdfContent)
    //         ->header('Content-Type', 'application/pdf')
    //         ->header('Content-Disposition', 'attachment; filename="orders.pdf"');
    // }

    // protected function generatePdf($orders)
    // {
    //     // Implement the actual PDF generation logic here
    //     // For example, use a library like Dompdf or TCPDF
    //     return 'PDF content'; // This is a placeholder. Return the actual PDF content.
    // }

    // public function order($id)
    // {
    //     $order = Order::findOrFail($id);
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => [
    //             'order' => $order
    //         ]
    //     ], 200);
    // }


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

    // public function updateProductDetails(Request $request)
    // {
    //     $request->validate([
    //         'order_products' => 'required|array',
    //         'order_products.*.id' => 'required|exists:order_products,id',
    //         'order_products.*.product_id' => 'required|exists:products,id',
    //         'order_products.*.quantity' => 'required|integer|min:1',
    //         'order_products.*.price' => 'required|numeric|min:0',
    //         'order_products.*.weight' => 'required|numeric|min:0',
    //     ]);

    //     foreach ($request->order_products as $productDetail) {
    //         $orderProduct = OrderProduct::find($productDetail['id']);
    //         $orderProduct->product_id = $productDetail['product_id'];
    //         $orderProduct->quantity = $productDetail['quantity'];
    //         $orderProduct->price = $productDetail['price'];
    //         $orderProduct->weight = $productDetail['weight'];
    //         $orderProduct->save();
    //     }

    //     return response()->json([
    //         'message' => 'Product details updated successfully.',
    //     ]);
    // }


      public function saveProductDetails(Request $request)
      
    {
        // dd($request);
        $validatedData = $request->validate([

            'order_id' => 'required|exists:orders,id', // Validate order_id
            'order_products' => 'required|array',
            'order_products.*.product_id' => 'required|exists:products,id',
            'order_products.*.quantity' => 'required|integer|min:1',
            'order_products.*.price' => 'required|numeric|min:0',
            'order_products.*.weight' => 'required|numeric|min:0',
        ]);
           // Get the validated order_id
    $order_id = $validatedData['order_id'];

        foreach ($validatedData['order_products'] as $productDetail) {
        

            // Check if the order product id exists in the table
            if (isset($productDetail['id']) && is_numeric($productDetail['id']) && OrderProduct::find($productDetail['id'])) {
                // Update existing order product
                $orderProduct = OrderProduct::find($productDetail['id']);

            } else {
                // Create new order product
                $orderProduct = new OrderProduct();
            }
            $orderProduct->order_id = $order_id;
            $orderProduct->product_id = $productDetail['product_id'];
            $orderProduct->quantity = $productDetail['quantity'];
            $orderProduct->price = $productDetail['price'];
            $orderProduct->weight = $productDetail['weight'];
            $orderProduct->save();
        }

        return response()->json([
            'message' => 'Product details saved successfully.',
        ]);
    }

    public function destroy($id)
    {
        $orderProduct = OrderProduct::find($id);

        if (!$orderProduct) {
            return response()->json([
                'message' => 'OrderProduct not found.'
            ], 404);
        }

        $orderProduct->delete();

        return response()->json([
            'message' => 'OrderProduct deleted successfully.'
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0'
        ]);

        $orderProduct = OrderProduct::create($validatedData);

        return response()->json([
            'message' => 'OrderProduct created successfully.',
            'orderProduct' => $orderProduct
        ], 201);
    }


    // public function bulkPrint(Request $request)
    // {

    //     // dd($request);
    //     // Ensure order_ids is provided and is an array
    //     if (!$request->has('order_ids') || !is_array($request->order_ids)) {
    //         return response()->json(['error' => 'Invalid order_ids'], 400);
    //     }
    
    //     // Fetch the orders you want to include in the PDF
    //     $orders = Order::whereIn('id', $request->order_ids)
    //         ->with('orderProducts') // Ensure items relationship is loaded
    //         ->get();



    //         if ($orders->isEmpty()) {
    //             return response()->json(['error' => 'No orders found for the provided IDs'], 404);
    //         }
        
        
    //     $pdf = Pdf::loadView('orders.waybill', ['orders' => $orders]);
    
    //     // Return the generated PDF
    //     return $pdf->download('orders.pdf');
    // }


    public function bulkPrint(Request $request)
    {
        // Validate that order_ids is provided and is an array
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        // Fetch the orders with their order_products relationship
        $orders = Order::whereIn('id', $request->order_ids)
            ->with('orderProducts') // Ensure the orderProducts relationship is loaded
            ->get();

        // Log the orders to verify data
        // Log::info('Fetched Orders:', $orders->toArray());

        // Check if orders were fetched
        if ($orders->isEmpty()) {
            return response()->json(['error' => 'No orders found for the provided IDs'], 404);
        }

        // Pass the orders to the view and generate PDF
        $pdf = Pdf::loadView('orders.waybill', ['orders' => $orders]);

        // Return the generated PDF as a stream
        return $pdf->stream('document.pdf');
    }




public function uploadPod(Request $request, $orderNo)
{
    // Validate the file
    $request->validate([
        'pod' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Validate file type and size
    ]);

    // Find the order
    $order = Order::where('order_no', $orderNo)->firstOrFail();

    // Store the POD file
    // $path = $request->file('pod')->store('pods', 'public'); // Store in 'public' disk
    $path = $request->file('pod')->store('pods', 'public');


    // Find or create the OrderPod record
    $orderPod = OrderPod::updateOrCreate(
        ['order_no' => $orderNo], // Conditions to find the existing record
        ['pod_path' => $path] // Fields to update or create
    );

    // Update the 'has_pod' column in the orders table
    $order->update(['pod' => 'Yes']);

    return response()->json(['message' => 'POD uploaded successfully']);
}
public function getPod($orderNo)
{
    // Fetch the POD details for the given order number
    $pod = OrderPod::where('order_no', $orderNo)->first();

    if ($pod) {
        $path = $pod->pod_path;
        $file = storage_path('app/public/' . $path);

        if (file_exists($file)) {
            // Stream the file as a response
            return response()->file($file);
        } else {
            return response()->json(['message' => 'File not found on the server'], 404);
        }
    } else {
        return response()->json(['message' => 'POD not found'], 404);
    }
}


    // Fetch POD
    // public function getPod($orderNo)
    // {
    //     // Fetch the POD details for the given order number
    //     $pod = OrderPod::where('order_no', $orderNo)->first();

    //     if ($pod) {
    //         return response()->json([
    //             'pod_path' => $pod->pod_path,
    //         ]);
    //     } else {
    //         return response()->json(['message' => 'POD not found'], 404);
    //     }
    // }

    // Delete POD
    public function destroyPod($orderNo)
    {
        // Delete the POD for the given order number
        $pod = OrderPod::where('order_no', $orderNo)->first();

        if ($pod) {
            // Delete the file from storage
            Storage::delete($pod->pod_path);

            // Delete the database record
            $pod->delete();

            return response()->json(['message' => 'POD deleted successfully']);
        } else {
            return response()->json(['message' => 'POD not found'], 404);
        }
    }
}



