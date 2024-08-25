<!-- Display the orders in a table -->
<table>
    <thead>
        <tr>
            <th>Created on</th>
            <th>Order NO</th>
            <th>Rider </th>
            <th>Vendor ID</th>
            <th>Driver ID</th>
            <th>POD Status</th>
            <th>Order Status</th>
            <th>Delivery Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $order)
        <tr>
            <td>{{ $order['created_at'] }}</td>

            <td>{{ $order['order_no'] }}</td>

            <td>{{ $order['rider']['name'] ?? 'N/A' }}</td>
            <td>{{ $order['vendor']['name'] ?? 'N/A' }}</td>
            <td>{{ $order['driver']['name'] ?? 'N/A' }}</td>
            <td>{{ $order['pod'] ?? 'N/A' }}</td>
            <th>{{ $order['status'] ?? 'N/A' }}</th>
            <td>{{ $order['delivery_date'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center;">No orders found for the specified criteria.</td>
        </tr>
        @endforelse
    </tbody>
</table>