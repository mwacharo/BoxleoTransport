<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dispatch List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .header, .footer {
            width: 100%;
            text-align: center;
       
        }

        .header {
            top: 0px;
        }

        .footer {
            bottom: 0px;
        }

        .footer .page:after {
            content: counter(page);
        }

        .total-info {
            margin-top: 20px;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-section div {
            width: 45%;
        }

        .signature {
            margin-top: 30px;
        }
        .header .contact-info {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div>
            <img src="/home/engineer/Desktop/BoxleoTransport/public/assets/img/icons/logo.png" alt="Boxleo Courier Logo" width="100" height="50">



            </div>

            <div class="contact-info">
        
                Boxleo Courier & Fulfillment Services Ltd<br>
                254746078049 / 254759142032<br>
                operations@boxleocourier.com<br>
                Akshrap Godowns Gate A-2, JKIA Junction
            </div>

      
        </div>
    <!-- Title -->
    <h3>Dispatch List</h3>
    <p><strong>Agent name: </strong> {{ isset($orders['rider']) && $orders['rider'] != null ? $orders['rider_id'] : 'N/A' }}</p>    

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Order No.</th>
                <th>Phone</th>
                <th>COD</th>
                <th>Shipping Charges</th>
                <th>Client</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $order['order_no'] }}</td>
                <td>{{ $order['phone'] }}</td>
                <td>{{ $order['cod_amount'] }}</td>
                <td>{{ $order['shipping_charges'] }}</td>
                <td>{{ $order['client_name'] }}</td>
                <td>{{ $order['address'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="total-info">
  
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div>
            <p>Dispatched By ________________________</p>
            <p class="signature">Signature ________________________</p>
        </div>
        <div>
            <p>Date ________________________</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p class="page">Page </p>
    </div>

</body>
</html>
