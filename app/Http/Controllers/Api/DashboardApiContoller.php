<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;


class DashboardApiContoller extends Controller


{


  public function fetchDashboardData()
      {

      $pendingDeliveriesCount = Order::where('status', 'In Transit')->count();
      $totalOrdersToday = Order::whereDate('created_at', today())->count();
      $completed = Order::where('status', 'Delivered')->count();
      $pendingDeliveries = Order::where('status', 'Pending')->count();


          // $revenueToday = Order::whereDate('created_at', today())->sum('total');
          // $companyGrowth = $this->calculateCompanyGrowth();
          $alerts = $this->getAlerts();

          return response()->json([


            'pending_deliveries_count' => $pendingDeliveriesCount,
           'total_orders_today' => $totalOrdersToday,
           'pending_deliveries' => $pendingDeliveries,
            'total_orders_today' => $totalOrdersToday,
              'completed' => $completed,
              // 'revenue_today' => $revenueToday,
              // 'company_growth' => $companyGrowth,
              'alerts' => $alerts,
          ]);
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
    // public function statistics()
    // {
    //     $pendingDealsCount = Deal::whereIn('status', ['initiated', 'inProgress'])->count();
    //
    //     $recentDeals = Deal::orderBy('created_at', 'desc')->take(5)->get();
    //
    //     $servicesStatistics = Service::all();
    //
    //     return response()->json([
    //         'pending_deals_count' => $pendingDealsCount,
    //         'recent_deals' => $recentDeals,
    //         'services_statistics' => $servicesStatistics,
    //     ]);
    // }
    //
    // // public function dealsAnnually(){
    //
    // // }
    // public function dealsAnnually()
    // {
    //     $deals = Deal::all(); // Retrieve all deals from the database
    //
    //     $monthlyCounts = [
    //         'InProgress' => array_fill(0, 9, 0), // Placeholder for each month (Jan-Sep)
    //         'Won' => array_fill(0, 9, 0),
    //         'Lost' => array_fill(0, 9, 0),
    //     ];
    //
    //     foreach ($deals as $deal) {
    //         // Extract month index (0-based) from the 'close_date' field
    //         $monthIndex = (int) date('n', strtotime($deal->close_date)) - 1;
    //
    //         // Increment the count based on deal status
    //         switch ($deal->status) {
    //             case 'InProgress':
    //                 $monthlyCounts['InProgress'][$monthIndex]++;
    //                 break;
    //             case 'Won':
    //                 $monthlyCounts['Won'][$monthIndex]++;
    //                 break;
    //             case 'Lost':
    //                 $monthlyCounts['Lost'][$monthIndex]++;
    //                 break;
    //             default:
    //                 // Handle other statuses if needed
    //                 break;
    //         }
    //     }
    //
    //     // Format data in a way suitable for ApexCharts
    //     $chartData = [
    //         'series' => array_values($monthlyCounts), // Use values of monthly counts
    //         'xaxis' => ['categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']],
    //     ];
    //
    //     return response()->json($chartData);
    // }
    //
    // public function dealStatusCounts()
    // {
    //     $deals = Deal::all();
    //     // dd($deals);
    //
    //     $statusCounts = [
    //         'Won' => 0,
    //         'Lost' => 0,
    //         'InProgress' => 0,
    //     ];
    //
    //     foreach ($deals as $deal) {
    //         switch ($deal->status) {
    //             case 'Won':
    //                 $statusCounts['Won']++;
    //                 break;
    //             case 'Lost':
    //                 $statusCounts['Lost']++;
    //                 break;
    //             case 'InProgress':
    //                 $statusCounts['InProgress']++;
    //                 break;
    //             case 'Initiated':
    //                 $statusCounts['InProgress']++;
    //                 break;
    //             default:
    //                 break;
    //         }
    //     }
    //     // Format data in a way suitable for doughnut chart
    //     $chartData = [
    //         ['status' => 'Won', 'value' => $statusCounts['Won']],
    //         ['status' => 'Lost', 'value' => $statusCounts['Lost']],
    //         ['status' => 'In Progress', 'value' => $statusCounts['InProgress']],
    //     ];
    //
    //     return response()->json($chartData);
    //
    // }
}
