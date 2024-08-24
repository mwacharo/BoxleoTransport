<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\GeocodingService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPod;
use App\Models\OrderProduct;
use App\Models\OrderProductInstance;
use App\Models\Product;
use App\Models\ProductInstance;
use App\Models\Rider;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;;

use Illuminate\Validation\ValidationException;


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



    // public function dispatchOrders(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'order_ids' => 'required|array|min:1',
    //             // 'order_ids.*' => 'required|string',
    //             'geofence_id' => 'required|integer',
    //             'status' => 'required|string|in:Dispatched,In Transit',
    //             'rider_id' => 'required_without:driver_id|integer|nullable',
    //             'driver_id' => 'required_without:rider_id|integer|nullable',
    //         ]);

    //         $orderIds = $validatedData['order_ids'];
    //         $geofenceId = $validatedData['geofence_id'];
    //         $status = $validatedData['status'];
    //         $riderId = $validatedData['rider_id'] ?? null;
    //         $driverId = $validatedData['driver_id'] ?? null;

    //         Log::info('Order IDs:', ['order_ids' => $validatedData['order_ids']]);


    //         $orderIds = $validatedData['order_ids'] ?? [];

    //         if (!is_array($orderIds) || count($orderIds) === 0) {
    //             throw new \Exception('No valid order IDs provided.');
    //         }

    //         DB::beginTransaction();



    //         foreach ($orderIds as $orderId) {
    //             $order = Order::findOrFail($orderId);
    //             $order->status = $status;
    //             $order->geofence_id = $geofenceId;

    //             if ($status === 'Dispatched') {
    //                 $order->rider_id = $riderId;
    //                 $order->driver_id = $driverId;
    //             } elseif ($status === 'In Transit') {
    //                 $this->sendSmsNotification($order->client_phone, 'Your order is now in transit!');

    //                 if ($riderId) {
    //                     $rider = Rider::findOrFail($riderId);
    //                     $order->rider_id = $riderId;
    //                     // Uncomment the following line when you're ready to send emails
    //                     // Mail::to($rider->email)->send(new InTransitReport($order));
    //                     Log::info("SMS sent to client: {$order->client_phone}, Email sent to rider: {$rider->email}");
    //                 } elseif ($driverId) {
    //                     $order->driver_id = $driverId;
    //                     // Add any driver-specific logic here
    //                 }


    //                 //  an order has  orderProducts  
    //                 foreach ($order->orderProducts as $orderProduct) {
    //                     $inventory = Product::where('id', $orderProduct->product_id)->firstOrFail();
    //                     if ($inventory->quantity_remaining < $orderProduct->quantity) {
    //                         throw new \Exception('Insufficient inventory for product ID: ' . $orderProduct->product_id);
    //                     }
    //                     $inventory->quantity_remaining -= $orderProduct->quantity;
    //                     $inventory->quantity_issued += $orderProduct->quantity;
    //                     $inventory->save();
    //                 }
    //             }

    //             $order->save();
    //         }

    //         DB::commit();
    //         return response()->json(['message' => 'Orders dispatched successfully'], 200);
    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error dispatching orders: ' . $e->getMessage(), ['exception' => $e]);
    //         return response()->json(['message' => 'Error dispatching orders: ' . $e->getMessage()], 500);
    //     }
    // }

    // private function sendSmsNotification($phone, $message)
    // {
    //     // Implement your SMS sending logic here
    //     // This is just a placeholder
    //     Log::info("SMS notification sent to $phone: $message");
    // }
    //     public function dispatchOrders(array $orderIds, $riderId, $driverId, $geofenceId, $status)
    // {
    //     DB::beginTransaction();

    //     try {
    //         foreach ($orderIds as $orderId) {
    //             // Fetch the order
    //             $order = Order::findOrFail($orderId);

    //             // Update the order status
    //             $order->status = $status;

    //             if ($status === 'Dispatched') {
    //                 // Update only rider_id and geofence_id if status is "Dispatched"
    //                 $order->rider_id = $riderId;
    //                 $order->geofence_id = $geofenceId;
    //             } elseif ($status === 'In Transit') {
    //                 // For "In Transit" status:
    //                 // SMS notification to the client
    //                 $this->sendSmsNotification($order->client_phone, 'Your order is now in transit!');

    //                 // Email in-transit report to the rider
    //                 $rider = Rider::findOrFail($riderId);
    //                 // Mail::to($rider->email)->send(new InTransitReport($order));

    //                 // Log that the notifications were sent
    //                 Log::info("SMS sent to client: {$order->client_phone}, Email sent to rider: {$rider->email}");

    //                 // Update inventory for each item in the order
    //                 foreach ($order->items as $item) {
    //                     $inventory = Product::where('product_id', $item->product_id)->firstOrFail();

    //                     if ($inventory->quantity_remaining < $item->quantity) {
    //                         throw new \Exception('Insufficient inventory for product ID: ' . $item->product_id);
    //                     }

    //                     // Subtract from quantity_remaining and add to quantity_issued
    //                     $inventory->quantity_remaining -= $item->quantity;
    //                     $inventory->quantity_issued += $item->quantity;
    //                     $inventory->save();
    //                 }

    //                 // Update rider_id and geofence_id for "In Transit" status
    //                 $order->rider_id = $riderId;
    //                 $order->geofence_id = $geofenceId;
    //             }

    //             // Save the order
    //             $order->save();
    //         }

    //         // Commit the transaction after processing all orders
    //         DB::commit();

    //         // Optionally, send notifications for each order
    //         // foreach ($orderIds as $orderId) {
    //         //     $order = Order::find($orderId);
    //         //     $order->client->notify(new OrderStatusNotification($order));
    //         // }
    //     } catch (\Exception $e) {
    //         // Rollback the transaction on error
    //         DB::rollBack();
    //         Log::error('Error dispatching orders: ' . $e->getMessage(), ['exception' => $e]);
    //         throw $e;
    //     }
    // }

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

    // pick order items 
    // result in update  of instance from available to sold 
    // decrease in product quanity remaining 
    // increase in quanity issued 


    // public function pickOrderitems(){

    // }

    // public function pickOrderitems(Request $request)
    // {
    //     $order = Order::findOrFail($request->order_id);
    //     $pickedItems = $request->picked_items;

    //     foreach ($pickedItems as $item) {
    //         $product = Product::findOrFail($item['product_id']);

    //         foreach ($item['instance_ids'] as $instanceId) {
    //             // Update the status of the product instance to "sold"
    //             $instance = ProductInstance::findOrFail($instanceId);
    //             $instance->status = 'sold';
    //             $instance->save();
    //         }

    //         // Decrease the product quantity in inventory
    //         $product->quantity_remaining -= count($item['instance_ids']);
    //         $product->save();

    //         // Increase the quantity issued (assuming you have a field for this)
    //         $product->quantity_issued += count($item['instance_ids']);
    //         $product->save();
    //     }

    //     return response()->json(['message' => 'Order items picked successfully.'], 200);
    // }


    public function pickOrderitem(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        // updated status of the order picked or packed 
        $pickedItems = $request->picked_items;

        foreach ($pickedItems as $item) {
            $product = Product::findOrFail($item['product_id']);

            foreach ($item['instance_ids'] as $instanceId) {
                // Update the status of the product instance to "sold"
                $instance = ProductInstance::findOrFail($instanceId);
                $instance->status = 'sold';
                $instance->save();

                // Register the record in the order_product_instances table
                OrderProductInstance::create([
                    'order_id' => $order->id,
                    'product_instance_id' => $instanceId,
                ]);
            }

            // Decrease the product quantity in inventory
            // rems
            $product->quantity_remaining -= count($item['instance_ids']);
            $product->save();

            // Increase the quantity issued
            $product->quantity_issued += count($item['instance_ids']);
            $product->save();
        }

        return response()->json(['message' => 'Order items picked and recorded successfully.'], 200);
    }


    public function dispatchOrders(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'order_ids' => 'required|array|min:1',
                // 'order_ids.*' => 'required|string',
                'geofence_id' => 'required|integer',
                'status' => 'required|string|in:Dispatched,In Transit',
                'rider_id' => 'required_without:driver_id|integer|nullable',
                'driver_id' => 'required_without:rider_id|integer|nullable',
            ]);

            $orderIds = $validatedData['order_ids'];
            $geofenceId = $validatedData['geofence_id'];
            $status = $validatedData['status'];
            $riderId = $validatedData['rider_id'] ?? null;
            $driverId = $validatedData['driver_id'] ?? null;

            Log::info('Order IDs:', ['order_ids' => $validatedData['order_ids']]);


            $orderIds = $validatedData['order_ids'] ?? [];

            if (!is_array($orderIds) || count($orderIds) === 0) {
                throw new \Exception('No valid order IDs provided.');
            }

            DB::beginTransaction();



            foreach ($orderIds as $orderId) {
                $order = Order::findOrFail($orderId);
                $order->status = $status;
                $order->geofence_id = $geofenceId;

                if ($status === 'Dispatched') {
                    $order->rider_id = $riderId;
                    $order->driver_id = $driverId;


                    Log::debug('Status set to dispatched_on for order', ['order_id' => $orderId]);
                    $order->dispatched_on =now();
                } elseif ($status === 'In Transit') {
                    $this->sendSmsNotification($order->client_phone, 'Your order is now in transit!');

                    if ($riderId) {
                        $rider = Rider::findOrFail($riderId);
                        $order->rider_id = $riderId;
                        // Uncomment the following line when you're ready to send emails
                        // Mail::to($rider->email)->send(new InTransitReport($order));
                        Log::info("SMS sent to client: {$order->client_phone}, Email sent to rider: {$rider->email}");
                    } elseif ($driverId) {
                        $order->driver_id = $driverId;
                        // Add any driver-specific logic here
                    }


                    //  an order has  orderProducts  
                    foreach ($order->orderProducts as $orderProduct) {
                        $inventory = Product::where('id', $orderProduct->product_id)->firstOrFail();
                        if ($inventory->quantity_remaining < $orderProduct->quantity) {
                            throw new \Exception('Insufficient inventory for product ID: ' . $orderProduct->product_id);
                        }
                        $inventory->quantity_remaining -= $orderProduct->quantity;
                        $inventory->quantity_issued += $orderProduct->quantity;
                        $inventory->save();
                    }

                       // updating dispatched_on 
                Log::debug('Status set to dispatched_on for order', ['order_id' => $orderId]);
                $order->dispatched_on =now();

                }

                $order->save();
            }

            DB::commit();
            return response()->json(['message' => 'Orders dispatched successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error dispatching orders: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error dispatching orders: ' . $e->getMessage()], 500);
        }
    }

    private function sendSmsNotification($phone, $message)
    {
        // Implement your SMS sending logic here
        // This is just a placeholder
        Log::info("SMS notification sent to $phone: $message");
    }


    // return an order 
    


    // public function returnOrders(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'order_ids' => 'required|array|min:1',
    //             'status' => 'required|string|in:Returned,Awaiting Return',
    //         ]);
    //         $orderIds = $validatedData['order_ids'];
    //         $status = $validatedData['status'];
    //         Log::info('Order IDs:', ['order_ids' => $validatedData['order_ids']]);

    //         if (!is_array($orderIds) || count($orderIds) === 0) {
    //             throw new \Exception('No valid order IDs provided.');
    //         }

    //         DB::beginTransaction();

    //         foreach ($orderIds as $orderId) {
    //             $order = Order::findOrFail($orderId);
    //             $order->status = $status;

    //             if ($status === 'Awaiting Returned') {
    //                 $order->status =  $status;

    //                 // update order status to awaiting return
    //             } elseif ($status === ' Returned') {
    //                 //  an order has  orderProducts  
    //                 foreach ($order->orderProducts as $orderProduct) {
    //                     $order->status =  $status;

    //                     $inventory = Product::where('id', $orderProduct->product_id)->firstOrFail();
    //                     $inventory->quantity_remaining += $orderProduct->quantity;
    //                     $inventory->return_quantity += $orderProduct->quantity;
    //                     $inventory->save();

    //                 }
    //             }
    //             $order->save();
    //         }

    //         DB::commit();
    //         return response()->json(['message' => 'Orders returned successfully'], 200);
    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error returning orders: ' . $e->getMessage(), ['exception' => $e]);
    //         return response()->json(['message' => 'Error returned orders: ' . $e->getMessage()], 500);
    //     }
    // }


    public function returnOrders(Request $request)
{
    try {
        Log::debug('Starting returnOrders method');

        $validatedData = $request->validate([
            'order_ids' => 'required|array|min:1',
            'status' => 'required|string|in:Returned,Awaiting Return',
        ]);

        $orderIds = $validatedData['order_ids'];
        $status = $validatedData['status'];

        Log::debug('Validated data', ['order_ids' => $orderIds, 'status' => $status]);

        if (!is_array($orderIds) || count($orderIds) === 0) {
            throw new \Exception('No valid order IDs provided.');
        }

        DB::beginTransaction();

        foreach ($orderIds as $orderId) {
            Log::debug('Processing order', ['order_id' => $orderId]);

            $order = Order::findOrFail($orderId);
            Log::debug('Order found', ['order' => $order->toArray()]);

            $order->status = $status;

            if ($status === 'Awaiting Return') {
                Log::debug('Status set to Awaiting Return for order', ['order_id' => $orderId]);
                // No additional action needed, status is already set
            } elseif ($status === 'Returned') {
                Log::debug('Status set to Returned for order', ['order_id' => $orderId]);

                foreach ($order->orderProducts as $orderProduct) {
                    Log::debug('Processing order product', ['order_product' => $orderProduct->toArray()]);

                    $inventory = Product::where('id', $orderProduct->product_id)->firstOrFail();
                    Log::debug('Product inventory found', ['inventory' => $inventory->toArray()]);

                    $inventory->quantity_remaining += $orderProduct->quantity;
                    $inventory->return_quantity += $orderProduct->quantity;
                    $inventory->save();

                    Log::debug('Inventory updated', ['inventory' => $inventory->toArray()]);
                }

                // updating return_on 
                Log::debug('Status set to return_on for order', ['order_id' => $orderId]);
                $order->returned_on =now();

            }
            $order->save();
            Log::debug('Order status updated and saved', ['order_id' => $orderId]);
        }

        DB::commit();
        Log::debug('Transaction committed successfully');
        return response()->json(['message' => 'Orders returned successfully'], 200);
    } catch (ValidationException $e) {
        Log::error('Validation failed', ['errors' => $e->errors()]);
        return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error returning orders: ' . $e->getMessage(), ['exception' => $e]);
        return response()->json(['message' => 'Error returning orders: ' . $e->getMessage()], 500);
    }
}


}
