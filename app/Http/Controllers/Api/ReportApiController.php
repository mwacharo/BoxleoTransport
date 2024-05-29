<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Deal;
use App\Exports\DealsExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportApiController extends Controller
{

    public function generate(Request $request)
    {
        DB::enableQueryLog();
        $deals = Deal::query();

        if ($request->filled('sales_person_id')) {
            $deals->whereHas('user', function($q) use ($request) {
                $q->where('user_id', $request->sales_person_id);
            });
        }
        if ($request->filled('branch_id')) {
            $deals->whereHas('branches', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('status')) {
            $deals->where('status', $request->status);
        }

        if ($request->filled('industry_id')) {
            $deals->whereHas('industries', function($q) use ($request) {
                $q->where('industry_id', $request->industry_id);
            });
        }

        if ($request->filled('service_id')) {
            $deals->whereHas('services', function($q) use ($request) {
                $q->where('service_id', $request->service_id);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $deals->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('priority')) {
            $deals->where('priority', $request->priority);
        }


        // Execute the query with eager loading of relationships
        $deals = $deals->with(['user', 'branches', 'services', 'industries'])->get();
        // return response()->json(
        //     [
        //         'deals' => $deals,
        //     ]
        // );
        // (DB::getQueryLog()); // Debug to check SQL queries generated

        // Generate the Excel file and save it
        Excel::store(new DealsExport($deals), 'public/report.xlsx');

        // Return the path or a token linked to the file
        return response()->json(['url' => asset('storage/report.xlsx')]);
    }

}

