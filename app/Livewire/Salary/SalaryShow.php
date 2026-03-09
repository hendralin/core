<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Salary;
use App\Models\Vehicle;
use App\Models\Salesman;
use Livewire\Attributes\Title;

#[Title('Detail Penggajian')]
class SalaryShow extends Component
{
    public Salary $salary;

    public function mount(Salary $salary): void
    {
        $this->salary = $salary->load([
            'employee.position',
            'salaryDetails.salaryComponent',
            'salaryDetails.vehicle.brand',
            'salaryDetails.vehicle.vehicle_model',
        ]);
    }

    public function render()
    {
        $salary = $this->salary;
        $totalSalary = $salary->salaryDetails->sum('total_amount');

        // Data penjualan mobil untuk Marketing (position_id = 1) pada bulan penggajian ini
        $marketingSalesSummaryForPeriod = null;
        $marketingSalesForPeriod = collect();

        $employee = $salary->employee;
        $salaryDate = $salary->salary_date;

        if ($employee && (int) ($employee->position_id ?? 0) === 1 && $salaryDate && $employee->user_id) {
            $salesman = Salesman::where('user_id', $employee->user_id)->first();

            if ($salesman) {
                $year = (int) $salaryDate->year;
                $month = (int) $salaryDate->month;

                $baseSalesQuery = Vehicle::query()
                    ->where('salesman_id', $salesman->id)
                    ->whereNotNull('selling_date')
                    ->whereYear('selling_date', $year)
                    ->whereMonth('selling_date', $month);

                $vehiclesSoldInPeriod = (clone $baseSalesQuery)->count();
                $totalSalesInPeriod = (clone $baseSalesQuery)->sum('selling_price');

                $averageSellingPriceInPeriod = $vehiclesSoldInPeriod > 0
                    ? $totalSalesInPeriod / $vehiclesSoldInPeriod
                    : 0;

                $marketingSalesSummaryForPeriod = [
                    'vehicles_sold_in_period' => $vehiclesSoldInPeriod,
                    'total_sales_in_period' => $totalSalesInPeriod,
                    'average_selling_price_in_period' => $averageSellingPriceInPeriod,
                ];

                $marketingSalesForPeriod = (clone $baseSalesQuery)
                    ->with(['brand', 'vehicle_model', 'type'])
                    ->orderBy('selling_date', 'desc')
                    ->limit(5)
                    ->get();
            }
        }

        return view('livewire.salary.salary-show', compact(
            'salary',
            'totalSalary',
            'marketingSalesSummaryForPeriod',
            'marketingSalesForPeriod',
        ));
    }
}
