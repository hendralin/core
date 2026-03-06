<?php

namespace App\Livewire\Loan\Employee;

use App\Models\EmployeeLoan;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Detail Pinjaman Karyawan')]
class EmployeeLoanShow extends Component
{
    public EmployeeLoan $employeeLoan;

    public function mount(EmployeeLoan $employeeLoan)
    {
        if ($employeeLoan->loan_type !== 'loan') {
            abort(404, 'Hanya pinjaman yang dapat dilihat dari halaman ini.');
        }

        $this->employeeLoan = $employeeLoan->load(['employee', 'cost.warehouse', 'createdBy']);
    }

    public function render()
    {
        return view('livewire.loan.employee.employee-loan-show');
    }
}
