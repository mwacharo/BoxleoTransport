<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// use Maatwebsite\Excel\Concerns\WithHeadings;


 class DynamicReportExport implements FromView 

// class DynamicReportExport implements FromArray, WithHeadings
{
    protected $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;

        // dd($reportData);
    }

    // public function array(): array
    // {
    //     return $this->reportData;
    // }

    // public function headings(): array
    // {
    //     // Assuming the first row contains the headers
    //     return count($this->reportData) > 0 ? array_keys($this->reportData[0]) : [];
    // }



    public function view(): View
    {
     


        return view('reports.pod', [
            'orders' => $this->reportData 
        ]);
    }
}