<?php

namespace App\Http\Controllers\Api;

use App\ModelsVendor;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductInstance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BaseController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductApiController extends BaseController
{



    protected $model = Product::class;

    //
    // public function index()
    // {
    //     // Fetch product instances with their bin details
    //     $productInstances = ProductInstance::with('bin')->get();
    //
    //     return response()->json(['instances' => $productInstances]);
    // }

    public function store(Request $request)
    {

        // Validate the request
        $this->validate($request, [
            'name' => 'required',
            'description' => 'string',
            'price' => 'required',
            'quantity' => 'nullable|numeric',
            'sku' => 'required',
            'vendor_id' => 'required',
        ]);

        // Create the product
        $product = Product::create($request->all());
        // Fetch the venxdor and its last identifier
        $vendor = Vendor::find($request->vendor_id);
        $vendorPrefix = strtoupper(substr($vendor->name, 0, 2)); // Example vendor prefix logic
        $skuPrefix = strtoupper(substr($request->sku, 0, 3)); // Example SKU prefix logic
        $lastIdentifier = $vendor->last_identifier;

        // Create the product instances with unique QR codes

        for ($i = 0; $i < $request->quantity; $i++) {
            $lastIdentifier++;
            $uniqueId = sprintf('%05d', $lastIdentifier); // Pads with leading zeros
            $uniqueIdentifier = "{$vendorPrefix}-{$skuPrefix}-{$uniqueId}";


            // Create product instance
            ProductInstance::create([
                'product_id' => $product->id,
                'barcode' => $uniqueIdentifier, // Store the file path
                'status' => 'available',
                'unique_identifier' => $uniqueIdentifier, // Assuming you have this column
            ]);
        }

        // Update the vendor's last identifier
        $vendor->last_identifier = $lastIdentifier;
        $vendor->save();




        // Return a JSON response
        return response()->json([
            'product' => $product,
            'message' => 'Product and instances created successfully'
        ]);
    }


    public function getProductInstances($id)
    {
        // Validate the product ID
        $product = Product::findOrFail($id);
        // return dd($product);

        // Fetch product instances
        $productInstances = ProductInstance::where('product_id', $id)
         ->with('bin')
        ->get();

        // Return the product instances as JSON
        return response()->json([
            // 'product' => $product,
            'instances' => $productInstances
        ]);
    }



    // Receive single product instance
    public function receiveSingle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'bin_location' => 'required|string',
        ]);

        $productInstance = ProductInstance::create([
            'product_id' => $request->product_id,
            'bin_location' => $request->bin_location,
            'status' => 'in_stock',
        ]);

        return response()->json(['success' => true, 'message' => 'Product instance received and assigned to bin']);
    }

    // Receive bulk product instances
    public function receiveBulk(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'bin_location' => 'required|string',
        ]);

        for ($i = 0; $i < $request->quantity; $i++) {
            ProductInstance::create([
                'product_id' => $request->product_id,
                'bin_location' => $request->bin_location,
                'status' => 'in_stock',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Product instances received and assigned to bin']);
    }

    // Assign bin to single product instance
    // public function bulkAssignBin(Request $request)
    // {
    //     $request->validate([
    //         'product_instance_id' => 'required|exists:product_instances,id',
    //         'bin_location' => 'required|string',
    //     ]);
    //
    //     $productInstance = ProductInstance::findOrFail($request->product_instance_id);
    //     $productInstance->bin_location = $request->bin_location;
    //     $productInstance->save();
    //
    //     return response()->json(['success' => true, 'message' => 'Bin assigned to product instance']);
    // }

    // Relocate single product instance
    public function relocate(Request $request)
    {
        $request->validate([
            'product_instance_id' => 'required|exists:product_instances,id',
            'new_bin_location' => 'required|string',
        ]);

        $productInstance = ProductInstance::findOrFail($request->product_instance_id);
        $productInstance->bin_location = $request->new_bin_location;
        $productInstance->save();

        return response()->json(['success' => true, 'message' => 'Product instance relocated']);
    }

    // Bulk assign bin to product instances
    public function bulkAssignBin(Request $request)
    {
        $request->validate([
            'product_instance_ids' => 'required|array',
            'product_instance_ids.*' => 'exists:product_instances,id',
            'bin_id' => 'required',
        ]);

        ProductInstance::whereIn('id', $request->product_instance_ids)
            ->update(['bin_location' => $request->bin_id]);

        return response()->json(['success' => true, 'message' => 'Bin assigned to product instances']);
    }

    // Bulk relocate product instances
    public function bulkRelocate(Request $request)
    {
        $request->validate([
            'product_instance_ids' => 'required|array',
            'product_instance_ids.*' => 'exists:product_instances,id',
            'new_bin_location' => 'required|string',
        ]);

        ProductInstance::whereIn('id', $request->product_instance_ids)
            ->update(['bin_location' => $request->new_bin_location]);

        return response()->json(['success' => true, 'message' => 'Product instances relocated']);
    }

    // Pick single product for order
    public function pick(Request $request)
    {
        $request->validate([
            'product_instance_id' => 'required|exists:product_instances,id',
            'order_id' => 'required|exists:orders,id',
        ]);

        $productInstance = ProductInstance::findOrFail($request->product_instance_id);
        $productInstance->status = 'picked';
        $productInstance->save();

        // Logic to associate the product instance with the order (not implemented here)

        return response()->json(['success' => true, 'message' => 'Product instance picked for order']);
    }

    // Pack single product for shipment
    public function pack(Request $request)
    {
        $request->validate([
            'product_instance_id' => 'required|exists:product_instances,id',
            'order_id' => 'required|exists:orders,id',
        ]);

        $productInstance = ProductInstance::findOrFail($request->product_instance_id);
        $productInstance->status = 'packed';
        $productInstance->save();

        // Logic to associate the product instance with the shipment (not implemented here)

        return response()->json(['success' => true, 'message' => 'Product instance packed for shipment']);
    }

    // Bulk pick products for order
    public function bulkPick(Request $request)
    {
        $request->validate([
            'product_instance_ids' => 'required|array',
            'product_instance_ids.*' => 'exists:product_instances,id',
            'order_id' => 'required|exists:orders,id',
        ]);

        ProductInstance::whereIn('id', $request->product_instance_ids)
            ->update(['status' => 'picked']);

        // Logic to associate the product instances with the order (not implemented here)

        return response()->json(['success' => true, 'message' => 'Product instances picked for order']);
    }

    // Bulk pack products for shipment
    // public function bulkPack(Request $request)
    // {
    //     $request->validate([
    //         'product_instance_ids' => 'required|array',
    //         'product_instance_ids.*' => 'exists:product_instances,id',
    //         'order_id' => 'required|exists:orders,id',
    //     ]);

    //     ProductInstance::whereIn('id', $request->product_instance_ids)
    //         ->update(['status' => 'packed']);

    //     // Logic to associate the product instances with the shipment (not implemented here)

    //     return response()->json(['success' => true, 'message' => 'Product instances packed for shipment']);
    // }


     // Adjust inventory quantity for a product instance
     public function adjustQuantity(Request $request)
     {
         $request->validate([
             'product_id' => 'required|exists:product_instances,id',
             'quantity' => 'required|integer',
         ]);

         $product = Product::findOrFail($request->product_instance_id);
         $product->quantity += $request->quantity;
         $product->save();

         return response()->json(['success' => true, 'message' => 'Prouduct quantity adjusted']);
     }

    //  // Check product availability
    //  public function checkAvailability(Request $request)
    //  {
    //      $request->validate([
    //          'product_instance_id' => 'required|exists:product_instances,id',
    //      ]);

    //      $productInstance = ProductInstance::findOrFail($request->product_instance_id);

    //      return response()->json(['success' => true, 'available_quantity' => $productInstance->quantity]);
    //  }

    //  // Bulk adjust inventory quantities
    //  public function bulkAdjustQuantity(Request $request)
    //  {
    //      $request->validate([
    //          'adjustments' => 'required|array',
    //          'adjustments.*.product_instance_id' => 'exists:product_instances,id',
    //          'adjustments.*.quantity' => 'integer',
    //      ]);

    //      foreach ($request->adjustments as $adjustment) {
    //          $productInstance = ProductInstance::findOrFail($adjustment['product_instance_id']);
    //          $productInstance->quantity += $adjustment['quantity'];
    //          $productInstance->save();
    //      }

    //      return response()->json(['success' => true, 'message' => 'Inventory quantities adjusted']);
    //  }

    //  // Bulk check product availability
    //  public function bulkCheckAvailability(Request $request)
    //  {
    //      $request->validate([
    //          'product_instance_ids' => 'required|array',
    //          'product_instance_ids.*' => 'exists:product_instances,id',
    //      ]);

    //      $availabilities = ProductInstance::whereIn('id', $request->product_instance_ids)
    //          ->get(['id', 'quantity']);

    //      return response()->json(['success' => true, 'availabilities' => $availabilities]);
    //  }

     // Transfer product instance to another warehouse
     public function transfer(Request $request)
     {
         $request->validate([
             'product_' => 'required|exists:product_id',
             'quantity' => 'required|exists:quantity',
             'new_warehouse_id' => 'required|exists:warehouses,id',
         ]);

         $product = Product::findOrFail($request->product_id);
         $product->warehouse_id = $request->new_warehouse_id;
         $produc->save();

         return response()->json(['success' => true, 'message' => 'Product instance transferred']);
     }

     // Bulk transfer product instances to another warehouse
     public function bulkTransfer(Request $request)
     {
         $request->validate([
             'product_instance_ids' => 'required|array',
             'product_instance_ids.*' => 'exists:product_instances,id',
             'new_warehouse_id' => 'required|exists:warehouses,id',
         ]);

         ProductInstance::whereIn('id', $request->product_instance_ids)
             ->update(['warehouse_id' => $request->new_warehouse_id]);

         return response()->json(['success' => true, 'message' => 'Product instances transferred']);
     }

     // Process return for a single product instance
     public function processReturn(Request $request)
     {
         $request->validate([
             'product_instance_id' => 'required|exists:product_instances,id',
         ]);

         $productInstance = ProductInstance::findOrFail($request->product_instance_id);
         $productInstance->status = 'in_stock';
         $productInstance->save();

         return response()->json(['success' => true, 'message' => 'Product instance returned and restocked']);
     }

     // Bulk process returns for multiple product instances
     public function bulkProcessReturns(Request $request)
     {
         $request->validate([
             'product_instance_ids' => 'required|array',
             'product_instance_ids.*' => 'exists:product_instances,id',
         ]);

         ProductInstance::whereIn('id', $request->product_instance_ids)
             ->update(['status' => 'in_stock']);

         return response()->json(['success' => true, 'message' => 'Product instances returned and restocked']);
     }

     // Perform cycle count for a single product instance
     public function performCycleCount(Request $request)
     {
         $request->validate([
             'product_instance_id' => 'required|exists:product_instances,id',
             'actual_quantity' => 'required|integer',
         ]);

         $productInstance = ProductInstance::findOrFail($request->product_instance_id);
         $productInstance->quantity = $request->actual_quantity;
         $productInstance->save();

         return response()->json(['success' => true, 'message' => 'Cycle count performed']);
     }

     // Audit inventory for a single product instance
     public function auditInventory(Request $request)
     {
         $request->validate([
             'product_instance_id' => 'required|exists:product_instances,id',
             'audit_notes' => 'required|string',
         ]);

         $productInstance = ProductInstance::findOrFail($request->product_instance_id);
         // Logic to store audit notes (not implemented here)

         return response()->json(['success' => true, 'message' => 'Inventory audited']);
     }

     // Bulk perform cycle counts
     public function bulkPerformCycleCounts(Request $request)
     {
         $request->validate([
             'cycle_counts' => 'required|array',
             'cycle_counts.*.product_instance_id' => 'exists:product_instances,id',
             'cycle_counts.*.actual_quantity' => 'integer',
         ]);

         foreach ($request->cycle_counts as $count) {
             $productInstance = ProductInstance::findOrFail($count['product_instance_id']);
             $productInstance->quantity = $count['actual_quantity'];
             $productInstance->save();
         }

         return response()->json(['success' => true, 'message' => 'Cycle counts performed']);
     }

     // Bulk audit inventory for multiple product instances
     public function bulkAuditInventory(Request $request)
     {
         $request->validate([
             'audits' => 'required|array',
             'audits.*.product_instance_id' => 'exists:product_instances,id',
             'audits.*.audit_notes' => 'string',
         ]);

         // Logic to store audit notes for each product instance (not implemented here)

         return response()->json(['success' => true, 'message' => 'Inventories audited']);
     }

     // Generate product report
     public function generateProductReport(Request $request)
     {
         $request->validate([
             'product_instance_id' => 'required|exists:product_instances,id',
         ]);

         $productInstance = ProductInstance::findOrFail($request->product_instance_id);
         // Logic to generate report (not implemented here)

         return response()->json(['success' => true, 'message' => 'Product report generated']);
     }

     // Track product movement
     public function trackProductMovement(Request $request)
     {
         $request->validate([
             'product_instance_id' => 'required|exists:product_instances,id',
         ]);

         $productInstance = ProductInstance::findOrFail($request->product_instance_id);
         // Logic to track movement (not implemented here)

         return response()->json(['success' => true, 'message' => 'Product movement tracked']);
     }

     // Generate bulk product reports
     public function generateBulkProductReports(Request $request)
     {
         $request->validate([
             'product_instance_ids' => 'required|array',
             'product_instance_ids.*' => 'exists:product_instances,id',
         ]);

         // Logic to generate reports (not implemented here)

         return response()->json(['success' => true, 'message' => 'Bulk product reports generated']);
     }

     // Track bulk product movements
     public function trackBulkProductMovement(Request $request)
     {
         $request->validate([
             'product_instance_ids' => 'required|array',
             'product_instance_ids.*' => 'exists:product_instances,id',
         ]);

         // Logic to track movements (not implemented here)

         return response()->json(['success' => true, 'message' => 'Bulk product movements tracked']);
     }

    //  // Create a single order
    //  public function createOrder(Request $request)
    //  {
    //      $request->validate([
    //          'customer_id' => 'required|exists:customers,id',
    //          'product_instance_ids' => 'required|array',
    //          'product_instance_ids.*' => 'exists:product_instances,id',
    //      ]);

    //      $order = Order::create([
    //          'customer_id' => $request->customer_id,
    //          'status' => 'pending',
    //      ]);

    //      // Logic to associate product instances with the order (not implemented here)

    //      return response()->json(['success' => true, 'message' => 'Order created']);
    //  }

    //  // Update order status
    //  public function updateOrderStatus(Request $request)
    //  {
    //      $request->validate([
    //          'order_id' => 'required|exists:orders,id',
    //          'status' => 'required|string',
    //      ]);

    //      $order = Order::findOrFail($request->order_id);
    //      $order->status = $request->status;
    //      $order->save();

    //      return response()->json(['success' => true, 'message' => 'Order status updated']);
    //  }

    //  // Bulk create orders
    //  public function bulkCreateOrders(Request $request)
    //  {
    //      $request->validate([
    //          'orders' => 'required|array',
    //          'orders.*.customer_id' => 'exists:customers,id',
    //          'orders.*.product_instance_ids' => 'array',
    //          'orders.*.product_instance_ids.*' => 'exists:product_instances,id',
    //      ]);

    //      foreach ($request->orders as $orderData) {
    //          $order = Order::create([
    //              'customer_id' => $orderData['customer_id'],
    //              'status' => 'pending',
    //          ]);

    //          // Logic to associate product instances with the order (not implemented here)
    //      }

    //      return response()->json(['success' => true, 'message' => 'Bulk orders created']);
    //  }

    //  // Bulk update order statuses
    //  public function bulkUpdateOrderStatuses(Request $request)
    //  {
    //      $request->validate([
    //          'orders' => 'required|array',
    //          'orders.*.order_id' => 'exists:orders,id',
    //          'orders.*.status' => 'string',
    //      ]);

    //      foreach ($request->orders as $orderData) {
    //          $order = Order::findOrFail($orderData['order_id']);
    //          $order->status = $orderData['status'];
    //          $order->save();
    //      }

    //      return response()->json(['success' => true, 'message' => 'Bulk order statuses updated']);
    //  }
 }
