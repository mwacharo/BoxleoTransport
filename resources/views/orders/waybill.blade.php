<!DOCTYPE html>
<html>
<head>
    <title>Waybill</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .waybill-container {
            width: 100%;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header img {
            height: 50px;
        }
        .header .contact-info {
            text-align: right;
        }
        .order-info, .shipping-info {
            margin: 20px 0;
        }
        .products table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .products table, .products th, .products td {
            border: 1px solid black;
        }
        .products th, .products td {
            padding: 10px;
            text-align: left;
        }
        .barcode {
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="waybill-container">
        <div class="header">
            <img src="/assets/img/icons/logo.png" alt="Boxleo Courier Logo">
            <div class="contact-info">
                <p><strong>Shipped From</strong><br>
                Boxleo Courier & Fulfillment Services Ltd<br>
                254746078049 / 254759142032<br>
                operations@boxleocourier.com<br>
                Akshrap Godowns Gate A-2, JKIA Junction</p>
            </div>
        </div>
        @foreach($orders as $order)
        <div class="order-info">
            <h2>Order No: {{ $order->order_no }}</h2>
            <p>Order Date: {{ $order->created_at }}</p>
            <p>Payment Method: {{ $order->payment_method ?? 'N/A' }}</p>
        </div>
        <div class="shipping-info">
            <p><strong>Ship To</strong><br>
            {{ $order->client_name }}<br>
            {{ $order->phone }}<br>
            {{ $order->address }}<br>
            {{ $order->city }}</p>
        </div>
        <div class="products">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Mode of Service</th>
                    </tr>
                </thead>
                <tbody>
                    @if($order->orderProducts && count($order->orderProducts) > 0)
                        @foreach($order->orderProducts as $orderProduct)
                            <tr>
                                <td>{{ $orderProduct->product->name }}</td>
                                <td>{{ $orderProduct->quantity }}</td>
                                <td>Delivery service</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3">No products found for this order.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="barcode">
            <p>{{ $order->order_no }}</p>
         <p>Total COD {{ $order->cod_amount }}</p>
            <!-- Include barcode generation logic here if necessary -->
        </div>
        <div class="footer">
            <p>Expected delivery date: {{ $order->delivery_date }}</p>
            <p>Terms & Conditions</p>
            <p>Payment is to be made on delivery via MPESA PAYBILL NO. 4032407. Account No: {{ $order->order_no }}</p>
        </div>
        <hr>
        @endforeach
    </div>
</body>
</html>
