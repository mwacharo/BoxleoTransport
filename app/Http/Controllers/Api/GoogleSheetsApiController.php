<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoogleSheetsApiController extends Controller
{


    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('LogixSaaS Google Sheets Integration');
        $this->client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->setAccessType('offline');
    }

    public function readSheets()
    {
        $service = new Google_Service_Sheets($this->client);
        $spreadsheetId = 'YOUR_SPREADSHEET_ID';
        $range = 'Sheet1!A1:R'; // Adjust the range as per your sheet structure

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            return response()->json(['message' => 'No data found in the spreadsheet.'], 404);
        }

        foreach ($values as $row) {
            Order::updateOrCreate(
                ['order_id' => $row[0]], // Assuming Order ID is unique
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

        return response()->json(['message' => 'Data imported successfully!'], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
