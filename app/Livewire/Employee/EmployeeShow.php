<?php

namespace App\Livewire\Employee;

use App\Models\Salary;
use App\Models\Vehicle;
use App\Models\Salesman;
use App\Models\EmployeeLoan;
use Livewire\Component;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Employee')]
class EmployeeShow extends Component
{
    use WithPagination;

    public Employee $employee;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function render()
    {
        $totalEmployeesCount = Employee::count();

        // Always get the total count of items in this employee (through employee_salary_components and salaries)
        $employeeTotalSalaryComponents = $this->employee->employeeSalaryComponents()->count();
        $employeeTotalSalaries = $this->employee->salaries()->count();

        // Get employee salary components with salary component information
        $employeeSalaryComponentsQuery = $this->employee->employeeSalaryComponents()
            ->with(['salaryComponent'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('salaryComponent', function($salaryComponentQuery) {
                          $salaryComponentQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('updated_at', 'desc');

        $employeeSalaryComponents = $employeeSalaryComponentsQuery->paginate($this->perPage);

        // Get filtered count for display
        $filteredCount = $this->search ? $employeeSalaryComponentsQuery->count() : $employeeTotalSalaryComponents;

        // Calculate pagination info
        $startEmployeeSalaryComponent = ($employeeSalaryComponents->currentPage() - 1) * $employeeSalaryComponents->perPage() + 1;
        $endEmployeeSalaryComponent = min($startEmployeeSalaryComponent + $employeeSalaryComponents->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startEmployeeSalaryComponent,
            'end' => $endEmployeeSalaryComponent,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        // Histori pinjaman: semua record (pinjaman + pembayaran) untuk karyawan ini
        $loanHistory = EmployeeLoan::where('employee_id', $this->employee->id)
            ->with(['cost.warehouse'])
            ->orderBy('paid_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $totalLoans = $this->employee->employeeLoans()->sum('amount');
        $totalPayments = $this->employee->employeeLoanPayments()->sum('amount');
        $remainingLoan = (float) ($this->employee->remaining_loan ?? 0);

        // Data penjualan mobil khusus untuk employee Marketing (position_id = 1) yang terhubung dengan salesman via user_id
        $marketingSalesSummary = null;
        $marketingSales = collect();

        if ((int) ($this->employee->position_id ?? 0) === 1 && $this->employee->user_id) {
            $salesman = Salesman::where('user_id', $this->employee->user_id)->first();

            if ($salesman) {
                $baseSalesQuery = Vehicle::query()
                    ->where('salesman_id', $salesman->id)
                    ->whereNotNull('selling_date');

                // Ringkasan total
                $totalVehiclesSold = (clone $baseSalesQuery)->count();
                $totalSalesAmount = (clone $baseSalesQuery)->sum('selling_price');

                // Ringkasan bulan berjalan
                $currentYear = now()->year;
                $currentMonth = now()->month;

                $monthlySalesQuery = (clone $baseSalesQuery)
                    ->whereYear('selling_date', $currentYear)
                    ->whereMonth('selling_date', $currentMonth);

                $vehiclesSoldThisMonth = (clone $monthlySalesQuery)->count();
                $salesThisMonth = (clone $monthlySalesQuery)->sum('selling_price');

                $averageSellingPrice = $totalVehiclesSold > 0
                    ? $totalSalesAmount / $totalVehiclesSold
                    : 0;

                $marketingSalesSummary = [
                    'total_vehicles_sold' => $totalVehiclesSold,
                    'total_sales_amount' => $totalSalesAmount,
                    'vehicles_sold_this_month' => $vehiclesSoldThisMonth,
                    'sales_this_month' => $salesThisMonth,
                    'average_selling_price' => $averageSellingPrice,
                ];

                // Ambil beberapa penjualan terbaru untuk ditampilkan
                $marketingSales = (clone $baseSalesQuery)
                    ->with(['brand', 'vehicle_model', 'type'])
                    ->orderBy('selling_date', 'desc')
                    ->limit(5)
                    ->get();
            }
        }

        return view('livewire.employee.employee-show', compact(
            'totalEmployeesCount',
            'employeeSalaryComponents',
            'filteredCount',
            'paginationInfo',
            'employeeTotalSalaryComponents',
            'employeeTotalSalaries',
            'loanHistory',
            'totalLoans',
            'totalPayments',
            'remainingLoan',
            'marketingSalesSummary',
            'marketingSales',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
