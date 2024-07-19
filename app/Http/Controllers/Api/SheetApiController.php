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

        $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->setApplicationName('boxleotransport');
        $client->setAccessType('offline');

        $service = new GoogleServiceSheets($client);
        $spreadsheetId = $sheet->post_spreadsheet_id;
        $range = $sheet->sheet_name;

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

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

    private function createOrder($data, $client, $user_id, $sheet)
    {
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
}
