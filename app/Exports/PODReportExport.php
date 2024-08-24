<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;

class PODReportExport implements FromView
{
    protected $agentId;
    protected $vehicleId;
    protected $startDate;
    protected $endDate;
    protected $cityId;
    protected $status;
    protected $vendorId;
    protected $driverId;

    public function __construct($filters)
    {
        $this->agentId = $filters['agent_id'] ?? null;
        $this->vehicleId = $filters['vehicle_id'] ?? null;
        $this->startDate = $filters['start_date'] ?? null;
        $this->endDate = $filters['end_date'] ?? null;
        $this->cityId = $filters['city_id'] ?? null;
        $this->status = $filters['status'] ?? null;
        $this->vendorId = $filters['vendor_id'] ?? null;
        $this->driverId = $filters['driver_id'] ?? null;
    }

    public function view(): View
    {
        $query = Order::query();

        // Date range filtering
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        // Agent filtering
        if ($this->agentId) {
            $query->whereIn('rider_id', $this->agentId);
        }

        // Vehicle filtering
        if ($this->vehicleId) {
            $query->where('vehicle_id', $this->vehicleId);
        }

        // City filtering
        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
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
        
        $orders = $query->with('vendor', 'rider')->get();

    dd($orders);


        return view('reports.pod', [
            'orders' => $orders
        ]);
    }
}
