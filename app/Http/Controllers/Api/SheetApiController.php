<?php

namespace App\Http\Controllers\Api;

use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleServiceSheets;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
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
            $this->syncGoogleSheetData($sheet);
            return response()->json(['message' => 'Sheet synced successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to sync sheet', 'error' => $e->getMessage()], 500);
        }
    }

    private function syncGoogleSheetData(Sheet $sheet)
    {
        $client = new GoogleClient();
        // $client->setAuthConfig(storage_path('google/credentials.json')); // Ensure the path is correct
        $client->setAuthConfig('/home/engineer/Desktop/BoxleoTransport/public/credentials.json'); // Ensure the path is correct
        // location of credentials
        // /home/engineer/Desktop/BoxleoTransport/public/credentials.json

        // $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->addScope(\Google\Service\Drive::DRIVE_READONLY);

        $client->setApplicationName('boxleotransport');
        $client->setAccessType('offline');

        $service = new GoogleServiceSheets($client);
        $spreadsheetId = $sheet->post_spreadsheet_id;

        // $range = 'Sheet1'; 
        $range = $sheet->sheet_name; 
        // dd($range);

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        dd($values);

        if (empty($values)) {
            throw new \Exception('No data found in the spreadsheet.');
        }

        foreach ($values as $row) {
            Order::updateOrCreate(
                // ['order_id' => $row[0]], // Assuming Order ID is unique
                [
                    'pod' => $row[1],
                    'client_name' => $row[2],
                    'address' => $row[3],
                    'country' => $row[4],
                    'phone' => $row[5],
                    'city' => $row[6],
                    'product_name' => $row[7],
                    'quantity' => $row[8],
                    'no_of_boxes' => $row[9],
                    'weight_kgs' => $row[10],
                    'status' => $row[11],
                    'delivery_date' => $row[12],
                    'special_instruction' => $row[13],
                    'distance' => $row[14],
                    'agent' => $row[15],
                    'pod_returned' => strtolower($row[16]) === 'yes' ? true : false,
                ]
            );
        }
    }
}
