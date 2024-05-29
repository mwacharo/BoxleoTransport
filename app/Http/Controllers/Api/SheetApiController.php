<?php

namespace App\Http\Controllers\Api;

use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleServiceSheets;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Client;
use App\Models\Sheet;
use App\Models\Order;

class SheetApiController extends BaseController
{
    protected $model = Sheet::class;

    public function index()
    {
        // Eager load the vendor relationship
        $sheets = Sheet::with('vendor')->get();
        return response()->json($sheets);
    }

    public function sync($id)
    {
        $sheet = Sheet::find($id);
        if (is_null($sheet)) {
            return response()->json(['message' => 'Sheet not found'], 404);
        }

        try {
            $syncedCount = $this->syncGoogleSheetData($sheet);
            return response()->json(['message' => "$syncedCount records synced successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to sync sheet', 'error' => $e->getMessage()], 500);
        }
    }

    private function syncGoogleSheetData(Sheet $sheet)
    {
        $client = new GoogleClient();
        $client->setAuthConfig('/home/engineer/Desktop/BoxleoTransport/public/credentials.json'); // Ensure the path is correct
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

        // first row contains headers
        $headers = array_shift($values);
        $syncedCount = 0;

        foreach ($values as $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Ensure array_combine has matching number of elements
            $row = array_pad($row, count($headers), null);
            $data = array_combine($headers, $row);

            // Client validation
            $client = Client::where('phone', $data['Phone'])
                // ->orWhere('alt_phone', $data['Alt phone'])
                // ->Where('client_name', $data['Client name'])

                ->first();

            if ($client) {
                // Update client details if found
                $client->update([
                    'name' => $data['Client name'],
                    'email' => $data['Email'] ?? null,
                    'phone' => $data['Phone'],
                    // 'alt_phone' => $data['Alt phone'] ?? null,
                    'address' => $data['Address'],
                    'city' => $data['City'] ?? null,
                    // Add other fields if necessary
                ]);
            } else {
                // Create new client if not found
                $client = Client::create([
                    'user_id' => 1, // Update as necessary
                    'ou_id' => 1, // Update as necessary
                    'name' => $data['Client name'],
                    'email' => $data['Email'] ?? null,
                    'phone' => $data['Phone'],
                    'alt_phone' => $data['Alt phone'] ?? null,
                    'address' => $data['Address'],
                    'city' => $data['City'] ?? null,
                    // Add other fields if necessary
                ]);
            }

            // Create or update the order
            Order::updateOrCreate(
                [
                    'order_no' => $data['Order Id'] ?? $data['Invoice Number'], // Ensure the correct key is used
                ],
                [
                    'client_id' => $client->id,
                    'client_name' => $data['Client name'],
                    'address' => $data['Address'],
                    'country' => $data['Receiver Country*'] ?? null,
                    'phone' => $data['Phone'],
                    'city' => $data['City'] ?? null,
                    'product_name' => $data['Product Name'],
                    'quantity' => intval($data['Quantity']),
                    'boxes' => intval($data['Boxes'] ?? null),
                    'weight' => floatval($data['WEIGHT'] ?? null),
                    'status' => $data['Status'],
                    'delivery_date' => $data['Delivery date'] ?? null,
                    'special_instruction' => $data['Special instructions'] ?? null,
                    'distance' => floatval($data['DISTANCE'] ?? null),
                    'invoice_value' => floatval($data['INVOICE VALUE'] ?? 0),
                    'pod_returned' => isset($data['DOCUMENTS RECEIVED ']) && strtolower($data['DOCUMENTS RECEIVED ']) === 'yes' ? 1 : 0,
                ]
            );
            $syncedCount++;
        }
        return $syncedCount;
    }
}
