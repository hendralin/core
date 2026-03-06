<?php

namespace App\Livewire\Loan\Employee\Payment;

use App\Models\EmployeeLoan;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Detail Pembayaran Pinjaman')]
class EmployeeLoanPaymentShow extends Component
{
    public EmployeeLoan $employeeLoanPayment;

    public function mount(EmployeeLoan $employeeLoanPayment)
    {
        if ($employeeLoanPayment->loan_type !== 'payment') {
            abort(404, 'Hanya pembayaran pinjaman yang dapat dilihat dari halaman ini.');
        }

        $this->employeeLoanPayment = $employeeLoanPayment->load(['employee', 'cost.warehouse', 'createdBy']);
    }

    public function render()
    {
        return view('livewire.loan.employee.payment.employee-loan-payment-show');
    }
}
