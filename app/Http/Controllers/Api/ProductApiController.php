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

    public function store(Request $request)
    {

        // Validate the request
        $this->validate($request, [
            'name' => 'required',
            'description' => 'string',
            'price' => 'required',
            'quantity' => 'required',
            'sku' => 'required',
            'vendor_id' => 'required',
        ]);

        // Create the product
        $product = Product::create($request->all());
        // Fetch the vendor and its last identifier
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
        $productInstances = ProductInstance::where('product_id', $id)->get();

        // Return the product instances as JSON
        return response()->json([
            // 'product' => $product,
            'instances' => $productInstances
        ]);
    }
}
