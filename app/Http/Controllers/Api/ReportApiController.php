<?php

namespace App\Http\Controllers\Api;

use App\Exports\DynamicReportExport;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportApiController extends Controller
{

    protected $reportClasses = [
        // 'Order Report' => \App\Exports\OrderReportExport::class,
        // 'Rider Report' => \App\Exports\RiderReportExport::class,
        'Dispatch Report' => \App\Exports\DispatchReportExport::class,
        // 'Rider Clearance' => \App\Exports\RiderClearanceExport::class,
        // 'Product Report' => \App\Exports\ProductReportExport::class,
        // 'Merchant Report' => \App\Exports\MerchantReportExport::class,
        // 'Delivery Performance Report' => \App\Exports\DeliveryPerformanceReportExport::class,
        // 'Agent/Driver Report' => \App\Exports\AgentDriverReportExport::class,
        // 'Financial Report' => \App\Exports\FinancialReportExport::class,
        // 'Vehicle Report' => \App\Exports\VehicleReportExport::class,
        // 'Client Report' => \App\Exports\ClientReportExport::class,
        // 'Zone Report' => \App\Exports\ZoneReportExport::class,
        'POD Report' => \App\Exports\PODReportExport::class,

    ];
    
    public function generateReport(Request $request)
    {
        $reportType = $request->input('report_type');

        if (!array_key_exists($reportType, $this->reportClasses)) {
            return response()->json(['error' => 'Invalid report type'], 400);
        }

        $reportClass = $this->reportClasses[$reportType];

        try {
            // Instantiate the report class to retrieve the data
            $reportInstance = new $reportClass($request->all());
            $reportData = $reportInstance->view()->getData()['orders']; // Assuming 'orders' is the key for the data


            return response()->json([
                'reportData' => $reportData,
                // 'url' => $url,
                'message' => 'Report generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Excel generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }
  
    public function downloadExcel(Request $request)
    {
        try {
            $reportData = $request->input('reportData');
            
            if (empty($reportData)) {
                return response()->json(['error' => 'No report data provided'], 400);
            }

            $export = new DynamicReportExport($reportData);
            
            return Excel::download($export, 'report.xlsx');
        } catch (\Exception $e) {
            Log::error('Excel generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate Excel: ' . $e->getMessage()], 500);
        }
    }
    
}


   



