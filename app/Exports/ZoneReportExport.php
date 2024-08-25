<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;


class ZoneReportExport implements FromView
{
    protected $agentId;
    protected $startDate;
    protected $endDate;
    protected $cityId;
    protected $status;
    protected $vendorId;
    protected $driverId;

    public function __construct($filters)
    {
        $this->cityId = $filters['city_id'] ?? null;
        $this->agentId = $filters['agent_id'] ?? null;
        $this->startDate = $filters['start_date'] ?? null;
        $this->endDate = $filters['end_date'] ?? null;
        $this->status = $filters['status'] ?? null;
        $this->vendorId = $filters['vendor_id'] ?? null;
    }

    public function view(): View
    {
        $query = Order::query();


        // Agent filtering

        // Date range filtering
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
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


        return view('reports.zone', [
            'orders' => $orders
        ]);
    }
}
