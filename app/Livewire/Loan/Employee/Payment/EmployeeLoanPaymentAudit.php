<?php

namespace App\Livewire\Loan\Employee\Payment;

use App\Models\Activity;
use App\Models\EmployeeLoan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Audit Trail Pembayaran Pinjaman')]
class EmployeeLoanPaymentAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedPayment = null;
    public $actionFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedPayment', 'actionFilter', 'dateFrom', 'dateTo'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedPayment', 'actionFilter']);
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject', 'subject.employee', 'subject.cost'])
            ->where('subject_type', EmployeeLoan::class)
            ->whereRaw("EXISTS (
                SELECT 1 FROM employee_loans el
                WHERE el.id = activity_log.subject_id
                AND el.loan_type = 'payment'
            )")
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', fn ($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('subject', function ($s) {
                            $s->where('description', 'like', '%' . $this->search . '%')
                                ->orWhereHas('employee', fn ($e) => $e->where('name', 'like', '%' . $this->search . '%'));
                        });
                });
            })
            ->when($this->selectedPayment, fn ($q) => $q->where('subject_id', $this->selectedPayment))
            ->when($this->actionFilter, fn ($q) => $q->where('description', $this->actionFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $paymentRecords = EmployeeLoan::with('employee')
            ->where('loan_type', 'payment')
            ->orderBy('paid_at', 'desc')
            ->get();

        $stats = [
            'total_activities' => Activity::where('subject_type', EmployeeLoan::class)
                ->whereRaw("EXISTS (SELECT 1 FROM employee_loans el WHERE el.id = activity_log.subject_id AND el.loan_type = 'payment')")
                ->count(),
            'today_activities' => Activity::where('subject_type', EmployeeLoan::class)
                ->whereRaw("EXISTS (SELECT 1 FROM employee_loans el WHERE el.id = activity_log.subject_id AND el.loan_type = 'payment')")
                ->whereDate('created_at', today())
                ->count(),
            'created_count' => Activity::where('subject_type', EmployeeLoan::class)
                ->where('description', 'created employee loan payment record')
                ->whereRaw("EXISTS (SELECT 1 FROM employee_loans el WHERE el.id = activity_log.subject_id AND el.loan_type = 'payment')")
                ->count(),
            'updated_count' => Activity::where('subject_type', EmployeeLoan::class)
                ->where('description', 'updated employee loan payment record')
                ->whereRaw("EXISTS (SELECT 1 FROM employee_loans el WHERE el.id = activity_log.subject_id AND el.loan_type = 'payment')")
                ->count(),
            'deleted_count' => Activity::where('subject_type', EmployeeLoan::class)
                ->where('description', 'deleted employee loan payment record')
                ->whereRaw("EXISTS (SELECT 1 FROM employee_loans el WHERE el.id = activity_log.subject_id AND el.loan_type = 'payment')")
                ->count(),
        ];

        return view('livewire.loan.employee.payment.employee-loan-payment-audit', compact('activities', 'paymentRecords', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }

    public function getActionOptionsProperty()
    {
        return [
            '' => 'Semua Aksi',
            'created employee loan payment record' => 'Dibuat',
            'updated employee loan payment record' => 'Diperbarui',
            'deleted employee loan payment record' => 'Dihapus',
        ];
    }
}
