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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SheetApiController extends BaseController
{
    protected $model = Sheet::class;

    public function index()
    {
        // Eager load the vendor relationship
        $sheets = Sheet::with('vendor')->get();
        return response()->json($sheets);
    }

    public function sync(Request $request ,$id)
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


    private function syncGoogleSheetData($sheet,$user_id)
    {

        $client = new GoogleClient();
        // $client->setAuthConfig('/home/engineer/Desktop/BoxleoTransport/public/credentials.json');
        $client->setAuthConfig('/var/www/BoxleoTransport/public/credentials.json'); // Ensure the path is correct

        $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->setApplicationName('boxleotransport');
        $client->setAccessType('offline');

        $service = new GoogleServiceSheets($client);
        $spreadsheetId = $sheet->post_spreadsheet_id;
        $range = $sheet->sheet_name;


     


        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        // dd($values);

        if (empty($values)) {
            throw new \Exception('No data found in the spreadsheet.');
        }

        $headers = array_shift($values);
        $headers = array_map('strtolower', $headers); // Normalize headers to lowercase

        $syncedCount = 0;
        $orderData = [];

        foreach ($values as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            $row = array_pad($row, count($headers), null);
            $data = array_combine($headers, $row);

            if (empty($data['order id']) && empty($data['invoice number'])) {
                continue;
            }

            $orderNo = $data['order id'] ?? null;
            $invoiceNo = $data['invoice number'] ?? null;

            if ($orderNo) {
                if (!isset($orderData[$orderNo])) {
                    $orderData[$orderNo] = [
                        'order_no' => $orderNo,
                        'invoice_no' => $invoiceNo,
                        'invoice_no' => $data['cod amount'],
                        'client_name' => $data['client name'],
                        'address' => $data['address'],
                        'country' => $data['receier country*'] ?? null,
                        'phone' => $data['phone'],
                        'city' => $data['city'] ?? null,
                        'products' => [],
                        // 'quantity' => $data['quantity'],

                        'status' => $data['status'],
                        'delivery_date' => $data['delivery date'] ?? null,
                        'special_instruction' => $data['special instructiuons'] ?? null,
                        'distance' => 0,
                        'invoice_value' => 0,
                        'pod_returned' => isset($data['documents received ']) && strtolower($data['documents received ']) === 'yes' ? 1 : 0,
                        'vendor_id' => $sheet->vendor_id,
                        'branch_id' => $sheet->branch_id,
                    ];
                }

                $orderData[$orderNo]['products'][] = [
                    'sku_number' => $data['sku number'],
                    'product_name' => $data['product name'],
                    'quantity' => $data['quantity'],
                    'boxes' => $data['boxes'],
                    'weight' => $data['weight'],
                ];
            }
        }

        DB::beginTransaction();

        try {
            foreach ($orderData as $orderNo => $data) {
                $client = Client::updateOrCreate([
                    'phone' => $data['phone'],
                ], [
                    'user_id' => $user_id,
                    'branch_id' => $sheet->ou_id,
                    'vendor_id' => $sheet->vendor_id,
                    'name' => $data['client_name'],
                    'email' => $data['email'] ?? null,
                    'alt_phone' => $data['alt phone'] ?? null,
                    'address' => $data['address'],
                    'city' => $data['city'] ?? null,
                ]);

                $order = Order::Create([
                    'order_no' => $orderNo,
                    'client_id' => $client->id,
                    'client_name' => $data['client_name'],
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
                    'branch_id' => $sheet->ou_id,
                    'vendor_id' => $sheet->vendor_id,
                ]);

                foreach ($data['products'] as $productData) {
                    $product = Product::Create([
                        'name' => $productData['product_name'],
                        'sku' => $productData['sku_number'],
                        'vendor_id' => $sheet->vendor_id,
                        'branch_id' => $sheet->branch_id,

                    ]);

                    OrderProduct::Create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        //  'quantity' => $data['quantity'],
                        'quantity' => $productData['quantity'], // Fixed: Use $productData instead of $data
                        'price' => 0,
                        'weight' => 0,

                    ], [
                       
                    ]);
                }

                $syncedCount++;
                GeocodeAddress::dispatch($order);

                // Dispatch order after creating/updating it
                // $this->dispatchOrder($order->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncing Google Sheet data: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }

        return $syncedCount;
    }
}




    // private function syncGoogleSheetData(Sheet $sheet)
    // {
    //     $client = new GoogleClient();
    //     $client->setAuthConfig('/home/engineer/Desktop/BoxleoTransport/public/credentials.json');
    //     // $client->setAuthConfig('/var/www/BoxleoTransport/public/credentials.json'); // Ensure the path is correct

    //     $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
    //     $client->setApplicationName('boxleotransport');
    //     $client->setAccessType('offline');

    //     $service = new GoogleServiceSheets($client);
    //     $spreadsheetId = $sheet->post_spreadsheet_id;
    //     $range = $sheet->sheet_name;

    //     $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    //     $values = $response->getValues();


    //     //  dd($values);

    //     if (empty($values)) {
    //         throw new \Exception('No data found in the spreadsheet.');
    //     }

    //     $headers = array_shift($values);
    //     $headers = array_map('strtolower', $headers); // Normalize headers to lowercase

    //     $syncedCount = 0;
    //     $orderData = [];

    //     foreach ($values as $row) {
    //         if (empty(array_filter($row))) {
    //             continue;
    //         }

    //         $row = array_pad($row, count($headers), null);
    //         $data = array_combine($headers, $row);

    //         // Ensure at least one identifier exists
    //         if (empty($data['order id']) && empty($data['invoice number'])) {
    //             continue; // Skip the record if neither identifier exists
    //         }

    //         $orderNo = $data['order id'] ?? null;
    //         $invoiceNo = $data['invoice number'] ?? null;

    //         // Combine data for orders with the same order_no
    //         if ($orderNo) {
    //             if (!isset($orderData[$orderNo])) {
    //                 $orderData[$orderNo] = [
    //                     'order_no' => $orderNo,
    //                     'invoice_no' => $invoiceNo,
    //                     'client_name' => $data['client name'],
    //                     'address' => $data['address'],
    //                     'country' => $data['receier country*'] ?? null,
    //                     'phone' => $data['phone'],
    //                     'city' => $data['city'] ?? null,
    //                     // if  order_no is repetead combine product available 
    //                     // 'product_names' => [],
    //                     'product_name' => $data['product name'],
    //                     'quantity' => $data['quantity'],

    //                     // 'quantity' => 0,
    //                     'boxes' => 0,
    //                     'weight' => 0,
    //                     'status' => $data['status'],
    //                     'delivery_date' => $data['delivery date'] ?? null,
    //                     'special_instruction' => $data['special instructiuons'] ?? null,
    //                     'distance' => 0,
    //                     'invoice_value' => 0,
    //                     'pod_returned' => isset($data['documents received ']) && strtolower($data['documents received ']) === 'yes' ? 1 : 0,
    //                 ];
    //             }

    //             // Combine product details
    //             // $orderData[$orderNo]['product_names'][] = $data['product name'];
    //             // $orderData[$orderNo]['quantity'] += intval($data['quantity'] ?? 0);
    //             // $orderData[$orderNo]['boxes'] += intval($data['boxes'] ?? 0);
    //             // $orderData[$orderNo]['weight'] += floatval($data['weight'] ?? 0);
    //             // $orderData[$orderNo]['distance'] += floatval($data['distance'] ?? 0);
    //             // $orderData[$orderNo]['invoice_value'] += floatval($data['invoice value'] ?? 0);
    //         }
    //     }

    //     // Insert or update orders in the database
    //     foreach ($orderData as $orderNo => $data) {
    //         $client = Client::where('phone', $data['phone'])->first();
    //         //  dd($client);


    //         $client = Client::updateOrCrdataeate([
    //             'user_id' => 1,
    //             'branch_id' => 1,
    //             'name' => $data['client_name'],
    //             'email' => $data['email'] ?? null,
    //             'phone' => $data['phone'],
    //             'alt_phone' => $data['alt phone'] ?? null,
    //             'address' => $data['address'],
    //             'city' => $data['city'] ?? null,
    //         ]);


    //         // create prouduct if  doesnot exist 


    //         // store product  data in order_product 


    //         $order = Order::Create(
    //             ['order_no' => $orderNo],
    //             [
    //                 'client_id' => $client->id,
    //                 'client_name' => $data['client_name'],
    //                 'address' => $data['address'],
    //                 'country' => $data['country'],
    //                 'phone' => $data['phone'],
    //                 'city' => $data['city'],
    //                 // 'product_name' => implode(', ', $data['product_names']),
    //                 // 'product_name' => $data['product_name'],
    //                 // 'quantity' => $data['quantity'],
    //                 // 'boxes' => $data['boxes'],
    //                 // 'weight' => $data['weight'],
    //                 'status' => $data['status'],
    //                 'delivery_date' => $data['delivery_date'],
    //                 'special_instruction' => $data['special_instruction'],
    //                 'distance' => $data['distance'],
    //                 'invoice_value' => $data['invoice_value'],
    //                 'pod_returned' => $data['pod_returned'],
    //             ]
    //         );
    //         $syncedCount++;
    //         GeocodeAddress::dispatch($order);
    //     }

    //     return $syncedCount;
    // }


    //         // create prouduct if  doesnot exist 


    //         // store product  data in order_product 


    //         $order = Order::Create(
    //             ['order_no' => $orderNo],
    //             [
    //                 'client_id' => $client->id,
    //                 'client_name' => $data['client_name'],
    //                 'address' => $data['address'],
    //                 'country' => $data['country'],
    //                 'phone' => $data['phone'],
    //                 'city' => $data['city'],
    //                 // 'product_name' => implode(', ', $data['product_names']),
    //                 // 'product_name' => $data['product_name'],
    //                 // 'quantity' => $data['quantity'],
    //                 // 'boxes' => $data['boxes'],
    //                 // 'weight' => $data['weight'],
    //                 'status' => $data['status'],
    //                 'delivery_date' => $data['delivery_date'],
    //                 'special_instruction' => $data['special_instruction'],
    //                 'distance' => $data['distance'],
    //                 'invoice_value' => $data['invoice_value'],
    //                 'pod_returned' => $data['pod_returned'],
    //             ]
    //         );
    //         $syncedCount++;
    //         GeocodeAddress::dispatch($order);
    //     }

    //     return $syncedCount;
    // }


