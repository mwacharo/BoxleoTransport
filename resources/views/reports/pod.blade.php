<!-- Display the orders in a table -->
<table>
    <thead>
        <tr>
            <th>Order NO</th>
            <th>Rider </th>
            <th>Vehicle ID</th>
            <th>Delivery Date</th>
            <th>Vendor ID</th>
            <th>Driver ID</th>
            <th>POD Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $order)
            <tr>
                <td>{{ $order['order_no'] }}</td>
                <td>{{ $order->rider->name ?? 'N/A' }}</td>
                <td>{{ $order['vehicle_id'] }}</td>
                <td>{{ $order['delivery_date'] }}</td>
                <td>{{ $order->vendor->name ?? 'N/A' }}</td>
                <td>{{ $order['driver_id'] }}</td>
                <td>{{ $order['pod'] ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align: center;">No orders found for the specified criteria.</td>
            </tr>
        @endforelse
    </tbody>
</table>
