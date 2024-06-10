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
    $orders=Order::all();
        // $orders= Order::with('vendor')->get();
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
    

}
