<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Carbon\Carbon;

class DashboardApiContoller extends Controller


{


    public function fetchDashboardData()
    {
        $pendingDeliveriesCount = Order::where('status', 'In Transit')->count();
        $totalOrdersToday = Order::whereDate('created_at', today())->count();
        $completed = Order::where('status', 'Delivered')->count();
        $pendingDeliveries = Order::where('status', 'Pending')->count();
        $alerts = $this->getAlerts();
    
        // Get delivery performance metrics
        $onTimeDeliveryRate = $this->calculateOnTimeDeliveryRate();
        $averageDeliveryTime = $this->calculateAverageDeliveryTime();
        $lateDeliveries = $this->calculateLateDeliveries();
        $deliveryTimeDistribution = $this->calculateDeliveryTimeDistribution();
    
        return response()->json([
            'pending_deliveries_count' => $pendingDeliveriesCount,
            'total_orders_today' => $totalOrdersToday,
            'pending_deliveries' => $pendingDeliveries,
            'completed' => $completed,
            'alerts' => $alerts,
            'on_time_delivery_rate' => $onTimeDeliveryRate,
            'average_delivery_time' => $averageDeliveryTime,
            'late_deliveries' => $lateDeliveries,
            'delivery_time_distribution' => $deliveryTimeDistribution,
        ]);
    }
    
    private function calculateAverageDeliveryTime()
    {
        $deliveryTimes = Order::whereNotNull('delivered_on')
            ->get()
            ->map(function ($order) {
                $deliveredOn = Carbon::parse($order->delivered_on);
                $createdAt = Carbon::parse($order->created_at);
                return $deliveredOn->diffInMinutes($createdAt);
            });
    
        return $deliveryTimes->isNotEmpty() ? $deliveryTimes->avg() : 0;
    }
    
    private function calculateOnTimeDeliveryRate()
    {
        // Assuming the function should calculate the rate of on-time deliveries
        $totalDelivered = Order::whereNotNull('delivered_on')->count();
        $onTimeDelivered = Order::whereNotNull('delivered_on')
            ->whereColumn('delivered_on', '<=', 'delivery_date')
            ->count();
            //  dd($onTimeDelivered);
    
        return $totalDelivered > 0 ? ($onTimeDelivered / $totalDelivered) * 100 : 0;
    }
    
    private function calculateLateDeliveries()
    {
        return Order::whereNotNull('delivered_on')
            ->whereColumn('delivered_on', '>', 'delivery_date')
            ->count();
    }
    
    private function calculateDeliveryTimeDistribution()
    {
        return Order::whereNotNull('delivered_on')
            ->get()
            ->groupBy(function ($order) {
                $deliveredOn = Carbon::parse($order->delivered_on);
                $createdAt = Carbon::parse($order->created_at);
                return $deliveredOn->diffInMinutes($createdAt);
            })
            ->map->count();
    }
    

    private function calculateCompanyGrowth()
    {
        // Implement the logic to calculate company growth
        return 15; // Placeholder value
    }

    private function getAlerts()
    {
        // Implement the logic to get alerts
        return [
            'Low inventory on item X',
            'Order #1234 is delayed',
        ]; // Placeholder values
    }

    private function getStatusId($statusName)
    {
        return OrderStatus::where('name', $statusName)->first()->id ?? null;
    }

    public function ordersAnnually()
    {
        // Retrieve all orders from the database
        $orders = Order::all();

        // Initialize monthly counts for each status
        $monthlyCounts = [
            'Pending' => array_fill(0, 12, 0), // Placeholder for each month (Jan-Dec)
            'Processing' => array_fill(0, 12, 0),
            'Ready for Pickup' => array_fill(0, 12, 0),
            'Picked Up' => array_fill(0, 12, 0),
            'In Transit' => array_fill(0, 12, 0),
            'Out for Delivery' => array_fill(0, 12, 0),
            'Delivered' => array_fill(0, 12, 0),
            'Failed Delivery' => array_fill(0, 12, 0),
            'Returned' => array_fill(0, 12, 0),
            'Cancelled' => array_fill(0, 12, 0),
            'On Hold' => array_fill(0, 12, 0),
            'Rescheduled' => array_fill(0, 12, 0),
            'Scheduled' => array_fill(0, 12, 0),
        ];

        // Iterate through each order to increment counts based on status and month
        foreach ($orders as $order) {
            // Extract month index (0-based) from the 'close_date' field
            $monthIndex = (int) date('n', strtotime($order->created_at)) - 1;

            // Increment the count based on order status
            switch ($order->status) {
                case 'Pending':
                    $monthlyCounts['Pending'][$monthIndex]++;
                    break;
                case 'Processing':
                    $monthlyCounts['Processing'][$monthIndex]++;
                    break;
                case 'Ready for Pickup':
                    $monthlyCounts['Ready for Pickup'][$monthIndex]++;
                    break;
                case 'Picked Up':
                    $monthlyCounts['Picked Up'][$monthIndex]++;
                    break;
                case 'In Transit':
                    $monthlyCounts['In Transit'][$monthIndex]++;
                    break;
                case 'Out for Delivery':
                    $monthlyCounts['Out for Delivery'][$monthIndex]++;
                    break;
                case 'Delivered':
                    $monthlyCounts['Delivered'][$monthIndex]++;
                    break;
                case 'Failed Delivery':
                    $monthlyCounts['Failed Delivery'][$monthIndex]++;
                    break;
                case 'Returned':
                    $monthlyCounts['Returned'][$monthIndex]++;
                    break;
                case 'Cancelled':
                    $monthlyCounts['Cancelled'][$monthIndex]++;
                    break;
                case 'On Hold':
                    $monthlyCounts['On Hold'][$monthIndex]++;
                    break;
                case 'Rescheduled':
                    $monthlyCounts['Rescheduled'][$monthIndex]++;
                    break;
                case 'Scheduled':
                    $monthlyCounts['Scheduled'][$monthIndex]++;
                    break;
                default:
                    // Handle other statuses if needed
                    break;
            }
        }

        // Format data in a way suitable for ApexCharts
        $chartData = [
            'series' => [
                ['name' => 'Pending', 'data' => $monthlyCounts['Pending']],
                ['name' => 'Processing', 'data' => $monthlyCounts['Processing']],
                ['name' => 'Ready for Pickup', 'data' => $monthlyCounts['Ready for Pickup']],
                ['name' => 'Picked Up', 'data' => $monthlyCounts['Picked Up']],
                ['name' => 'In Transit', 'data' => $monthlyCounts['In Transit']],
                ['name' => 'Out for Delivery', 'data' => $monthlyCounts['Out for Delivery']],
                ['name' => 'Delivered', 'data' => $monthlyCounts['Delivered']],
                ['name' => 'Failed Delivery', 'data' => $monthlyCounts['Failed Delivery']],
                ['name' => 'Returned', 'data' => $monthlyCounts['Returned']],
                ['name' => 'Cancelled', 'data' => $monthlyCounts['Cancelled']],
                ['name' => 'On Hold', 'data' => $monthlyCounts['On Hold']],
                ['name' => 'Rescheduled', 'data' => $monthlyCounts['Rescheduled']],
                ['name' => 'Scheduled', 'data' => $monthlyCounts['Scheduled']],
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ],
        ];

        // Return the formatted data as a JSON response
        return response()->json($chartData);
    }


    public function orderStatusCounts()
    {
        // Fetch all orders
        $orders = Order::all();

        // Initialize the status counts array with the status names
        $statusCounts = [
            'Pending' => 0,
            'Processing' => 0,
            'Ready for Pickup' => 0,
            'Picked Up' => 0,
            'In Transit' => 0,
            'Out for Delivery' => 0,
            'Delivered' => 0,
            'Failed Delivery' => 0,
            'Returned' => 0,
            'Cancelled' => 0,
            'On Hold' => 0,
            'Rescheduled' => 0,
            'Scheduled' => 0,

        ];

        // Count the orders by their status
        foreach ($orders as $order) {
            switch ($order->status) {
                case 'Pending':
                    $statusCounts['Pending']++;
                    break;
                case 'Processing':
                    $statusCounts['Processing']++;
                    break;
                case 'Ready for Pickup':
                    $statusCounts['Ready for Pickup']++;
                    break;
                case 'Picked Up':
                    $statusCounts['Picked Up']++;
                    break;
                case 'In Transit':
                    $statusCounts['In Transit']++;
                    break;
                case 'Out for Delivery':
                    $statusCounts['Out for Delivery']++;
                    break;
                case 'Delivered':
                    $statusCounts['Delivered']++;
                    break;
                case 'Failed Delivery':
                    $statusCounts['Failed Delivery']++;
                    break;
                case 'Returned':
                    $statusCounts['Returned']++;
                    break;
                case 'Cancelled':
                    $statusCounts['Cancelled']++;
                    break;
                case 'On Hold':
                    $statusCounts['On Hold']++;
                    break;
                case 'Rescheduled':
                    $statusCounts['Rescheduled']++;
                    break;
                case 'Scheduled':
                    $statusCounts['Scheduled']++;
                    break;
                default:
                    break;
            }
        }

        // Format data in a way suitable for a chart
        $chartData = [];
        foreach ($statusCounts as $status => $count) {
            $chartData[] = ['status' => $status, 'value' => $count];
        }

        // Return the data as JSON
        return response()->json($chartData);
    }



    // private function getAlerts()
    // {
    //     return [
    //         'Low inventory on item X',
    //         'Order #1234 is delayed',
    //     ];
    // }
}



