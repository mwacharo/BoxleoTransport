<?php

namespace App\Http\Controllers\Api;

use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleServiceSheets;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Client;
use App\Models\Sheet;
use App\Models\Order;
use App\Jobs\GeocodeAddress;
use App\Models\OrderProduct;
use App\Models\Product;
use Exception;
use Google\Service\Sheets\ValueRange;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use Google\Service\Sheets\BatchUpdateValuesRequest;


class SheetApiController extends BaseController
{
    protected $model = Sheet::class;

    public function index()
    {
        // Eager load the vendor relationship
        $sheets = Sheet::with('vendor')->get();
        return response()->json($sheets);
    }

    public function sync(Request $request, $id)
    {

        $user_id = $request->input('user_id');

        $sheet = Sheet::find($id);
        if (is_null($sheet)) {
            return response()->json(['message' => 'Sheet not found'], 404);
        }

        try {
            $syncedCount = $this->syncGoogleSheetData($sheet, $user_id);
            return response()->json(['message' => "$syncedCount records synced successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to sync sheet', 'error' => $e->getMessage()], 500);
        }
    }

    private function syncGoogleSheetData($sheet, $user_id)
    {
        // Google Sheets API initialization...
        $client = new GoogleClient();
        $client->setAuthConfig('/home/engineer/Desktop/BoxleoTransport/public/credentials.json');
        // $client->setAuthConfig('/var/www/BoxleoTransport/public/credentials.json'); // Ensure the path is correct
        // $client->setAuthConfig(base_path(config('services.google.credentials_path')));


        $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->setApplicationName('boxleotransport');
        $client->setAccessType('offline');

        $service = new GoogleServiceSheets($client);
        $spreadsheetId = $sheet->post_spreadsheet_id;
        $range = $sheet->sheet_name;

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            // dd($values);

            if (empty($values)) {
                throw new \Exception('No data found in the spreadsheet.');
            }

            $headers = array_map('strtolower', array_shift($values));
            $orderData = $this->processSheetData($values, $headers, $sheet);

            return $this->saveOrderData($orderData, $user_id, $sheet);
        } catch (\Exception $e) {
            Log::error('Error in syncGoogleSheetData: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    private function processSheetData($values, $headers, $sheet)
    {
        $orderData = [];

        foreach ($values as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            $row = array_pad($row, count($headers), null);
            $data = array_combine($headers, $row);

            $orderNo = $data['order id'] ?? null;
            $invoiceNo = $data['invoice number'] ?? null;

            if (empty($orderNo) && empty($invoiceNo)) {
                continue;
            }

            if (!isset($orderData[$orderNo])) {
                $orderData[$orderNo] = $this->createOrderData($data, $sheet);
            }

            $orderData[$orderNo]['products'][] = $this->createProductData($data);
        }
        // dd($orderData);

        return $orderData;
    }

    private function createOrderData($data, $sheet)
    {
        $cod_amount = str_replace(',', '', $data['cod amount'] ?? null);
        return [
            'order_no' => $data['order id'] ?? null,
            'invoice_no' => $data['invoice number'] ?? null,
            // 'cod_amount' => $data['cod amount'] ?? null,
            'cod_amount' => is_numeric($cod_amount) ? $cod_amount : null,
            'client_name' => $data['client name'] ?? null,
            'address' => $data['address'] ?? null,
            'country' => $data['receier country*'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'products' => [],
            'quantity' => $data['quantity'] ?? null,
            'status' => $data['status'] ?? null,
            'delivery_date' => $data['delivery date'] ?? null,
            'special_instruction' => $data['special instructiuons'] ?? null,
            'distance' => 0,
            'invoice_value' => 0,
            'pod_returned' => isset($data['documents received ']) && strtolower($data['documents received ']) === 'yes' ? 1 : 0,
            'vendor_id' => $sheet->vendor_id,
            'branch_id' => $sheet->branch_id,
        ];
    }

    private function createProductData($data)
    {
        return [
            'sku_number' => $data['sku number'] ?? null,
            'product_name' => $data['product name'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'boxes' => $data['boxes'] ?? null,
            'weight' => $data['weight'] ?? null,
        ];
    }

    private function saveOrderData($orderData, $user_id, $sheet)
    {
        $syncedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($orderData as $orderNo => $data) {
                $client = $this->createOrUpdateClient($data, $user_id, $sheet);
                $order = $this->createOrder($data, $client, $user_id, $sheet);
                $this->createOrderProducts($data['products'], $order, $sheet);

                $syncedCount++;
                GeocodeAddress::dispatch($order);
            }

            DB::commit();
            return $syncedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving order data: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    private function createOrUpdateClient($data, $user_id, $sheet)
    {
        return Client::updateOrCreate(
            ['phone' => $data['phone']],
            [
                'user_id' => $user_id,
                'branch_id' => $sheet->branch_id,
                'vendor_id' => $sheet->vendor_id,
                'name' => $data['client_name'],
                'email' => $data['email'] ?? null,
                'alt_phone' => $data['alt phone'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'] ?? null,
            ]
        );
    }
    // create receive function  or pickup address 

    private function createOrder($data, $client, $user_id, $sheet)
    {


        $existingOrder = Order::where('order_no', $data['order_no'])->first();


        if ($existingOrder) {

            // Order already exists, skip inserting a new one

            return $existingOrder;
        }

        return Order::create([
            'order_no' => $data['order_no'],
            'client_id' => $client->id,
            'client_name' => $data['client_name'],
            'cod_amount' => $data['cod_amount'],
            'address' => $data['address'],
            'country' => $data['country'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'status' => $data['status'],
            'delivery_date' => $data['delivery_date'],
            'special_instruction' => $data['special_instruction'],
            'distance' => $data['distance'],
            'invoice_value' => $data['invoice_value'],
            'pod_returned' => $data['pod_returned'],
            'user_id' => $user_id,
            'branch_id' => $sheet->branch_id,
            'vendor_id' => $sheet->vendor_id,
        ]);
    }



    private function createOrderProducts($products, $order, $sheet)
    {
        $orderProducts = [];

        foreach ($products as $productData) {
            try {
                $product = Product::updateOrCreate(
                    [
                        'name' => $productData['product_name'],
                        'sku' => $productData['sku_number'],
                        'vendor_id' => $sheet->vendor_id,
                        'branch_id' => $sheet->branch_id,
                    ]
                );

                $weight = !empty($productData['weight']) && is_numeric($productData['weight'])
                    ? intval($productData['weight'])
                    : 0;

                $quantity = !empty($productData['quantity']) && is_numeric($productData['quantity'])
                    ? intval($productData['quantity'])
                    : 1;

                $orderProducts[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => 0,
                    'weight' => $weight,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                Log::info('Prepared OrderProduct', [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'weight' => $weight,
                ]);
            } catch (\Exception $e) {
                Log::error('Error preparing OrderProduct', [
                    'error' => $e->getMessage(),
                    'product_data' => $productData,
                ]);
            }
        }

        if (!empty($orderProducts)) {
            OrderProduct::insert($orderProducts);
            Log::info('Inserted OrderProducts', ['count' => count($orderProducts)]);
        }
    }



    public function updateSheet(Request $request)
{
    try {
        $validatedData = $this->validateRequest($request);
        $vendorId = $validatedData['item']['vendor_id'];
        $sheetName = $validatedData['item']['sheet_name'];
        $spreadsheetId = $validatedData['item']['post_spreadsheet_id'];

        $orders = $this->fetchOrders($vendorId);
        
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found for this vendor'], 204);
        }

        $updateData = $this->prepareOrderData($orders);
        $result = $this->updateGoogleSheet($spreadsheetId, $sheetName, $updateData);

        return $this->prepareResponse($result, $orders);
    } catch (\Exception $e) {
        Log::error('Error updating sheet: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred while updating the sheet'], 500);
    }
}

// Debugging Update Sheet Function

private function prepareOrderData($orders)
{
    $updateData = [];

    foreach ($orders as $order) {
        $updateData[$order->order_no] = [
            'status' => $order->status ??'',
            'pod' => $order->pod ?? '',
            // 'special_instructions' => $order->special_instruction ?? '',
        ];
    }

    Log::debug("Prepared update data for " . count($updateData) . " orders");
    ($updateData);

    return $updateData;
}

private function updateGoogleSheet($spreadsheetId, $sheetName, $updateData)
{
    try {
        $client = $this->setupGoogleClient();
        $service = new GoogleServiceSheets($client);

        // First, get the existing data from the sheet
        $range = "$sheetName!A:T";  // Adjust range as needed to cover all your columns
        //  dd($range);
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        // dd($response);
        $sheetData = $response->getValues();

        // dd($sheetData);

        if (empty($sheetData)) {
            throw new \Exception("No data found in the sheet");
        }

        // Find the column indexes for Order No, Status, POD, and Special Instructions
        $headerRow = $sheetData[0];
        $orderIdIndex = array_search('Order No', $headerRow);
        $statusIndex = array_search('Status', $headerRow);
        $podIndex = array_search('POD', $headerRow);
        // $specialInstructionsIndex = array_search('Special Instruction', $headerRow);

        // dd($specialInstructionsIndex);

        // if ($orderIdIndex === false || $statusIndex === false || $podIndex === false || $specialInstructionsIndex === false) {
            if ($orderIdIndex === false || $statusIndex === false || $podIndex === false) {

            throw new \Exception("Required columns not found in the sheet");
        }

        $updates = [];

        foreach ($sheetData as $rowIndex => $row) {
            // Skip header row
            if ($rowIndex === 0) {
                Log::debug("Skipping header row");
                continue;
            }
        
            // Check if Order ID column exists
            if (!isset($row[$orderIdIndex])) {
                Log::error("Missing Order ID at row index $rowIndex");
                continue;
            }
        
            $orderId = $row[$orderIdIndex];
            Log::debug("Processing row index $rowIndex with Order ID: $orderId");
        
            // Check if there is an update for this Order ID
            if (isset($updateData[$orderId])) {
                $rowNumber = $rowIndex + 1; // 1-based index for Google Sheets
                Log::debug("Order ID $orderId found in update data, preparing updates for row number $rowNumber");
        
                // Add update for Status
                $updates[] = [
                    'range' => "$sheetName!S$rowNumber",
                    'values' => [[$updateData[$orderId]['status']]]
                ];
                Log::debug("Update added for Status at range $sheetName!S$rowNumber with value: " . $updateData[$orderId]['status']);
        
                // Add update for POD
                $updates[] = [
                    'range' => "$sheetName!R$rowNumber",
                    'values' => [[$updateData[$orderId]['pod']]]
                ];

                $updates[] = [
                    'range' => "$sheetName!U$rowNumber",
                    'values' => [[$updateData[$orderId]['special_instruction']]]
                ];
                Log::debug("Update added for POD at range $sheetName!P$rowNumber with value: " . $updateData[$orderId]['pod']);
            } else {
                Log::debug("Order ID $orderId not found in update data");
            }
        }
        

        // foreach ($sheetData as $rowIndex => $row) {
        //     // Skip header row
        //     if ($rowIndex === 0) {
        //         Log::debug("Skipping header row");
        //         continue;
        //     }
        
        //     // Fetch the Order ID
        //     $orderId = $row[$orderIdIndex];
        //     Log::debug("Processing row index $rowIndex with Order ID: $orderId");
        
        //     // Check if there is an update for this Order ID
        //     if (isset($updateData[$orderId])) {
        //         $rowNumber = $rowIndex + 1; // 1-based index for Google Sheets
        //         Log::debug("Order ID $orderId found in update data, preparing updates for row number $rowNumber");
        
        //         // Add update for Status
        //         $updates[] = [
        //             // 'range' => $sheetName.'!S'.$statusIndex+1,
        //             'range' => "$sheetName!S$rowNumber",

        //             'values' => [[$updateData[$orderId]['status']]]
        //         ];
        //         Log::debug("Update added for Status at range $sheetName!$statusIndex$rowNumber with value: " . $updateData[$orderId]['status']);
        
        //         // Add update for POD
        //         $updates[] = [
        //             // 'range' => "$sheetName!$podIndex$rowNumber",
        //             'range' => "$sheetName!P$rowNumber",

        //             'values' => [[$updateData[$orderId]['pod']]]
        //         ];
        //         Log::debug("Update added for POD at range $sheetName!$podIndex$rowNumber with value: " . $updateData[$orderId]['pod']);
        
        //         // Optional: Add update for Special Instructions (if implemented)
        //         // $updates[] = [
        //         //     'range' => "$sheetName!$specialInstructionsIndex$rowNumber",
        //         //     'values' => [[$updateData[$orderId]['special_instructions']]]
        //         // ];
        //         // Log::debug("Update added for Special Instructions at range $sheetName!$specialInstructionsIndex$rowNumber with value: " . $updateData[$orderId]['special_instructions']);
        //     } else {
        //         Log::debug("Order ID $orderId not found in update data");
        //     }
        // }
        

        if (!empty($updates)) {
            $batchUpdateRequest = new BatchUpdateValuesRequest([
                'valueInputOption' => 'RAW',
                'data' => $updates
            ]);

            $result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $batchUpdateRequest);
            Log::debug("Updated " . $result->getTotalUpdatedCells() . " cells");
            return $result;
        } else {
            Log::info("No matching orders found for update");
            return null;
        }
    } catch (\Exception $e) {
        Log::error("Error in updateGoogleSheet: " . $e->getMessage());
        throw $e;
    }

    
}
//     public function updateSheet(Request $request)
//     {
//         try {
//             $validatedData = $this->validateRequest($request);
//             $vendorId = $validatedData['item']['vendor_id'];
//             $sheetName = $validatedData['item']['sheet_name'];
//             $spreadsheetId = $validatedData['item']['post_spreadsheet_id'];
    
//             Log::debug("Validated data:", $validatedData);
    
//             $orders = $this->fetchOrders($vendorId);
//             Log::debug("Fetched orders count: " . $orders->count());
    
//             if ($orders->isEmpty()) {
//                 return response()->json(['message' => 'No orders found for this vendor'], 204);
//             }
    
//             $values = $this->prepareOrderData($orders);
//             Log::debug("Prepared order data count: " . count($values));
    
//             $result = $this->updateGoogleSheet($spreadsheetId, $sheetName, $values);
//             Log::debug("Google Sheet update result:", (array) $result);
    
//             return $this->prepareResponse($result, $orders);
//         } catch (\Exception $e) {
//             Log::error('Error updating sheet: ' . $e->getMessage());
//             Log::error('Stack trace: ' . $e->getTraceAsString());
//             return response()->json(['error' => 'An error occurred while updating the sheet'], 500);
//         }
//     }
    
    private function validateRequest(Request $request)
    {
        $validatedData = $request->validate([
            'item.vendor_id' => 'required|integer',
            'item.sheet_name' => 'required|string',
            'item.post_spreadsheet_id' => 'required|string',
        ]);
        Log::debug("Request validation passed");
        return $validatedData;
    }
    
    private function fetchOrders($vendorId)
    {
        $orders = Order::where('vendor_id', $vendorId)
            ->with(['products', 'orderProducts.product'])
            ->paginate(1000);
        Log::debug("Fetched orders for vendor $vendorId: " . $orders->count());
        return $orders;
    }
    
//     private function prepareOrderData($orders)
//     {
//         $header = ['Order No', 'Status', 'POD', 'Special Instructions', 'Client Name', 'Phone', 'Address', 'City', 'Products'];

//         // Order date	Order ID	Cod Amount	Client name	Address	Phone	Alt phone	Receier Country*	City	sku number	Product Name	Quantity	Boxes	Weight	Distance	TAT	EDT	Status	Delivery date	Special Instructiuons	Agent Number																																							
//         $values = [$header];
    
//         foreach ($orders as $order) {
//             $productNames = $order->orderProducts->pluck('product.name')->implode(', ');
//             $values[] = [
//                 // $order->created_at,
//                 $order->order_no,
//                 // $order->order_type,
//                 // $order->cod_amount,
//                 // $order->client_name,
//                 // $order->address,
//                 // $order->country,
//                 // $order->phone,
//                 // $order->alt_phone,	
//                 $order->status,
//                 $order->pod ?? '',
//                 $order->special_instruction ?? '',
//                 $order->client_name,
//                 $order->phone,
//                 $order->address,
//                 $order->city,
//                 $productNames,
//             ];
//         }
    
//         Log::debug("Prepared order data rows: " . (count($values) - 1));
//         return $values;
//     }
    


//     private function updateGoogleSheet($spreadsheetId, $sheetName, $values)
// {
//     try {
//         $client = $this->setupGoogleClient();
//         $service = new GoogleServiceSheets($client);
//         $range = "$sheetName!A1:I" . (count($values));

//         $body = new ValueRange(['values' => $values]);
//         $params = ['valueInputOption' => 'RAW'];

//         Log::debug("Attempting to update Google Sheet: $spreadsheetId, Range: $range");
//         Log::debug("Request body:", (array) $body);
//         Log::debug("Request params:", $params);

//         $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);

//         if ($result === null) {
//             Log::error("Google Sheets API returned null result");
//             throw new \Exception("Null result from Google Sheets API");
//         }

//         Log::debug("Google Sheet update completed. Result:", (array) $result);

//         return $result;
//     } catch (\Google_Service_Exception $e) {
//         Log::error("Google Sheets API Error: " . $e->getMessage());
//         Log::error("Error details: ", $e->getErrors());
//         throw $e;
//     } catch (\Exception $e) {
//         Log::error("Error in updateGoogleSheet: " . $e->getMessage());
//         Log::error("Stack trace: " . $e->getTraceAsString());
//         throw $e;
//     }
// }

private function setupGoogleClient(): GoogleClient
{
    try {
        $client = new GoogleClient();
        $client->setApplicationName('YourAppName');
        $client->setScopes([GoogleServiceSheets::SPREADSHEETS]);

    //   $client->setAuthConfig('/home/engineer/Desktop/BoxleoTransport/public/credentials.json');

      $credentialsPath='/home/engineer/Desktop/BoxleoTransport/public/credentials.json';

        // $credentialsPath = config('services.google.credentials_path');
        Log::debug("Credentials path: " . $credentialsPath);
        
        if (!file_exists($credentialsPath)) {
            Log::error("Google API credentials file not found at: " . $credentialsPath);
            throw new \Exception("Google API credentials file not found");
        }
        
        $client->setAuthConfig($credentialsPath);
        Log::debug("Google Client setup completed");
        return $client;
    } catch (\Exception $e) {
        Log::error("Error in setupGoogleClient: " . $e->getMessage());
        throw $e;
    }
}
    
    private function prepareResponse($result, $orders)
    {
        $response = [
            'success' => true,
            // 'updatedRows' => $result->getUpdatedRows(),
            'updatedRows' => $result->getTotalUpdatedRows(),  // Correct method to get the updated rows
            'totalOrders' => $orders->total(),
            'currentPage' => $orders->currentPage(),
            'lastPage' => $orders->lastPage(),
        ];
        Log::debug("Prepared response:", $response);
        return response()->json($response);
    }
}



