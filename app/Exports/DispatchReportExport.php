<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;


class DispatchReportExport implements FromView
{
    protected $agentId;
    protected $startDate;
    protected $endDate;
    protected $cityId;
    protected $status;
    protected $vendorId;
    protected $driverId;
    protected $dispatched_startDate;
    protected $dispatched_endDate;

    public function __construct($filters)
    {
        $this->cityId = $filters['city_id'] ?? null;
        $this->agentId = $filters['agent_id'] ?? null;
        $this->driverId = $filters['driver_id'] ?? null;
        $this->startDate = $filters['start_date'] ?? null;
        $this->endDate = $filters['end_date'] ?? null;
        $this->dispatched_startDate = $filters['dispatched_startDate'] ?? null;
        $this->dispatched_endDate = $filters['dispatched_onDate'] ?? null;
        $this->status = $filters['status'] ?? null;
        $this->vendorId = $filters['vendor_id'] ?? null;
    }

    public function view(): View
    {
        $query = Order::query();


        if ($this->dispatched_startDate && $this->dispatched_endDate) {
        // $this->dispatched_startDate = $filters['dispatched_onDate'] ?? null;
        $query->whereBetween('dispatched_on', [$this->dispatched_startDate, $this->dispatched_endDate]);
        }




        // if ($this->startDate && $this->endDate) {
        //     $query->whereBetween('dispatched_on', [$this->startDate, $this->endDate]);
        // }


        // Date range filtering
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }


        // filter by driver 

        if($this->driverId){
            $query->whereIn('driver_id', $this->driverId);
        }
        // Agent filtering
        if ($this->agentId) {
            $query->whereIn('rider_id', $this->agentId);
        }

        // City filtering
        if ($this->cityId) {
            $query->where('geofence_id', $this->cityId);
        }

        // Order status filtering
        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->vendorId) {
            $query->where('vendor_id', $this->vendorId);
        }


        // $query = Order::query()->whereBetween('created_at', [$this->startDate, $this->endDate]);
        // Log the raw SQL query and bindings
        Log::info('Generated SQL Query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // $orders = $query->get();

        $orders = $query->with('vendor', 'rider', 'driver', 'zone')->get();




        //  dd($orders);


        return view('reports.dispatch', [
            'orders' => $orders
        ]);
    }
}
