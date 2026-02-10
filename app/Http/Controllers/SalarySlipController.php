<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalarySlipController extends Controller
{
    /**
     * Generate and download salary slip PDF.
     */
    public function show(Salary $salary): StreamedResponse
    {
        $salary->load([
            'employee.position',
            'salaryDetails.salaryComponent',
            'salaryDetails.vehicle.brand',
            'salaryDetails.vehicle.vehicle_model',
        ]);
        $pdf = Pdf::loadView('exports.salary-slip-pdf', ['salary' => $salary]);
        $name = $salary->employee?->name ?? 'karyawan';
        $period = $salary->salary_date?->format('Y-m') ?? 'n-a';
        $filename = 'slip-gaji-' . preg_replace('/[^a-zA-Z0-9\-_.]/', '-', $name) . '-' . $period . '.pdf';
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
