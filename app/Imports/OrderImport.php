<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class OrderImport implements ToCollection
{


    public $orderData = [];
    public $products = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {
            // Skip the first row (headers)
            if ($key === 0) {
                continue;
            }

            // Collect order data
            $this->orderData[] = [
                'order_no' => $row[1] ?? null, // Order No
                'cod_amount' => $row[2] ?? null, // COD Amount
                'client_name' => $row[3] ?? null, // Client Name
                'address' => $row[4] ?? null, // Address
                'phone' => $row[5] ?? null, // Phone
                'alt_phone' => $row[6] ?? null, // Alt Phone
                'city' => $row[8] ?? null, // City
                'product_name' => $row[10] ?? null, // Product Name
                'quantity' => $row[11] ?? null, // Quantity
                'status' => $row[18] ?? null, // Status
                'delivery_date' => $row[19] ?? null, // Delivery Dat
            ];

            // Collect product data
            $this->products[] = [
                'product_name' => $row[10] ?? null, // Product Name
                'sku_number' => $row[9] ?? null,    // SKU Number
                'quantity' => $row[11] ?? 1,        // Quantity (default to 1 if null)
                'weight' => $row[13] ?? 0,          // Weight (default to 0 if null)
            ];
        }
    }
//     /**
//     * @param Collection $collection
//     */


//     public function collection(Collection $rows)
//     {
//         foreach ($rows as $key => $row) {
//             // Skip the first row (headers)
//             if ($key === 0) {
//                 continue;
//             }

//             Order::create([
//                 'order_date' => $row[0],
//                 'order_no' => $row[1],
//                 'cod_amount' => $row[2],
//                 'client_name' => $row[3],
//                 'address' => $row[4],
//                 'phone' => $row[5],
//                 'alt_phone' => $row[6],
//                 'receiver_country' => $row[7],
//                 'city' => $row[8],
//                 'sku_number' => $row[9],
//                 'product_name' => $row[10],
//                 'quantity' => $row[11],
//                 'boxes' => $row[12],
//                 'weight' => $row[13],
//                 'distance' => $row[14],
//                 'tat' => $row[15],
//                 'edt' => $row[16],
//                 'pod' => $row[17],
//                 'status' => $row[18],
//                 'delivery_date' => $row[19],
//                 'special_instructions' => $row[20],
//                 'agent_number' => $row[21],
//             ]);
//         }
// }
}
