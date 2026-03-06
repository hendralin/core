<?php

namespace App\Livewire\Loan\Employee\Payment;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Pembayaran Pinjaman Karyawan')]
class EmployeeLoanPaymentIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $paymentIdToDelete = null;
    public $search = '';
    public $sortField = 'paid_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $employeeFilter = '';
    public $employeeFilterSearch = '';
    public $warehouseFilter = '';
    public $dateFrom;
    public $dateTo;
    public $selectedMonthYear;
    public $bigCashFilter = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage', 'employeeFilter', 'employeeFilterSearch', 'warehouseFilter', 'dateFrom', 'dateTo', 'selectedMonthYear', 'bigCashFilter'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function setPaymentToDelete($paymentId)
    {
        $this->paymentIdToDelete = $paymentId;
    }

    public function delete()
    {
        try {
            if (!$this->paymentIdToDelete) {
                session()->flash('error', 'Tidak ada pembayaran yang dipilih untuk dihapus.');
                return;
            }

            $payment = EmployeeLoan::with(['employee', 'cost'])->findOrFail($this->paymentIdToDelete);

            if ($payment->loan_type !== 'payment') {
                session()->flash('error', 'Hanya pembayaran pinjaman yang dapat dihapus dari halaman ini.');
                return;
            }

            DB::transaction(function () use ($payment) {
                // Kembalikan remaining_loan karyawan (tambah lagi)
                $payment->employee->increment('remaining_loan', $payment->amount);

                // Hapus cost dan payment jika ada (big_cash = false)
                if ($payment->cost_id) {
                    $payment->cost?->delete();
                }

                $payment->delete();

                activity()
                    ->performedOn($payment)
                    ->causedBy(Auth::user())
                    ->withProperties(['old' => $payment->toArray()])
                    ->log('deleted employee loan payment record');
            });

            $this->reset(['paymentIdToDelete']);
            session()->flash('success', 'Pembayaran pinjaman berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function setEmployeeFilter($id)
    {
        $this->employeeFilter = $id ? (string) $id : '';
        $this->employeeFilterSearch = '';
    }

    public function clearEmployeeFilter()
    {
        $this->employeeFilter = '';
        $this->employeeFilterSearch = '';
    }

    public function clearFilters()
    {
        $this->reset(['search', 'employeeFilter', 'employeeFilterSearch', 'warehouseFilter', 'bigCashFilter']);
        $this->selectedMonthYear = null;
        $this->updateDateRange();
        $this->resetPage();
    }

    public function updatedSelectedMonthYear()
    {
        $this->updateDateRange();
    }

    private function updateDateRange()
    {
        if ($this->selectedMonthYear) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $this->selectedMonthYear);
            $this->dateFrom = $date->startOfMonth()->format('Y-m-d');
            $this->dateTo = $date->endOfMonth()->format('Y-m-d');
        } else {
            $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
            $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function render()
    {
        $payments = EmployeeLoan::query()
            ->where('loan_type', 'payment')
            ->with(['employee', 'cost.warehouse', 'createdBy'])
            ->when(
                $this->search,
                fn ($q) => $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('employee', fn ($e) => $e->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('createdBy', fn ($u) => $u->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->employeeFilter, fn ($q) => $q->where('employee_id', $this->employeeFilter))
            ->when($this->warehouseFilter, fn ($q) => $q->whereHas('cost', fn ($c) => $c->where('warehouse_id', $this->warehouseFilter)))
            ->when($this->bigCashFilter !== '', fn ($q) => $q->where('big_cash', $this->bigCashFilter === '1'))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $totalForFilters = EmployeeLoan::query()
            ->where('loan_type', 'payment')
            ->when(
                $this->search,
                fn ($q) => $q->where(function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('employee', fn ($e) => $e->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('createdBy', fn ($u) => $u->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->when($this->employeeFilter, fn ($q) => $q->where('employee_id', $this->employeeFilter))
            ->when($this->warehouseFilter, fn ($q) => $q->whereHas('cost', fn ($c) => $c->where('warehouse_id', $this->warehouseFilter)))
            ->when($this->bigCashFilter !== '', fn ($q) => $q->where('big_cash', $this->bigCashFilter === '1'))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('paid_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('paid_at', '<=', $this->dateTo))
            ->sum('amount');

        $employees = Employee::query()
            ->when($this->employeeFilterSearch !== '', fn ($q) => $q->where('name', 'like', '%' . trim($this->employeeFilterSearch) . '%'))
            ->orderBy('name')
            ->limit(50)
            ->get();
        $selectedEmployee = $this->employeeFilter ? Employee::find($this->employeeFilter) : null;
        $warehouses = Warehouse::where('has_cash', true)->orderBy('name')->get();

        return view('livewire.loan.employee.payment.employee-loan-payment-index', compact('payments', 'totalForFilters', 'employees', 'selectedEmployee', 'warehouses'));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
