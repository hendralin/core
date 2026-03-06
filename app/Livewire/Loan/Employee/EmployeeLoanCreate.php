<?php

namespace App\Livewire\Loan\Employee;

use App\Models\Cost;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Warehouse;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Tambah Pinjaman Karyawan')]
class EmployeeLoanCreate extends Component
{
    public $employee_id;
    public $employee_search = '';
    public $warehouse_id;
    public $paid_at;
    public $amount;
    public $description;
    public $big_cash = false;

    public function mount()
    {
        $this->paid_at = now()->format('Y-m-d');
    }

    public function submit()
    {
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'paid_at' => 'required|date|before_or_equal:today',
            'amount' => 'required|string',
            'description' => 'required|string',
            'big_cash' => 'nullable|boolean',
        ];

        $messages = [
            'employee_id.required' => 'Karyawan harus dipilih.',
            'employee_id.exists' => 'Karyawan tidak ditemukan.',
            'paid_at.required' => 'Tanggal pinjaman harus dipilih.',
            'paid_at.date' => 'Tanggal pinjaman harus berupa tanggal.',
            'paid_at.before_or_equal' => 'Tanggal pinjaman tidak boleh lebih dari hari ini.',
            'amount.required' => 'Jumlah pinjaman harus diisi.',
            'amount.string' => 'Jumlah pinjaman harus berupa angka.',
            'description.required' => 'Deskripsi harus diisi.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ];

        if (!$this->big_cash) {
            $rules['warehouse_id'] = 'required|exists:warehouses,id';
            $messages['warehouse_id.required'] = 'Kas/Warehouse harus dipilih untuk pinjaman dari Kas Kecil.';
            $messages['warehouse_id.exists'] = 'Warehouse yang dipilih tidak valid.';
        }

        $this->validate($rules, $messages);

        $amount = (float) Str::replace(',', '', $this->amount);
        if ($amount <= 0) {
            $this->addError('amount', 'Jumlah pinjaman harus lebih dari 0.');
            return;
        }

        try {
            DB::transaction(function () use ($amount) {
                $costId = null;

                if (!$this->big_cash) {
                    $cost = Cost::create([
                        'cost_type' => 'loan',
                        'vehicle_id' => null,
                        'warehouse_id' => $this->warehouse_id,
                        'cost_date' => $this->paid_at,
                        'vendor_id' => null,
                        'description' => $this->description ?? 'Pinjaman karyawan: ' . (Employee::find($this->employee_id)->name ?? ''),
                        'total_price' => $amount,
                        'document' => null,
                        'big_cash' => false,
                        'status' => 'approved',
                        'created_by' => Auth::id(),
                    ]);

                    $cost->payments()->create([
                        'payment_date' => $this->paid_at,
                        'amount' => $amount,
                        'note' => $this->description,
                    ]);

                    $costId = $cost->id;
                }

                $loan = EmployeeLoan::create([
                    'loan_type' => 'loan',
                    'employee_id' => $this->employee_id,
                    'paid_at' => $this->paid_at,
                    'cost_id' => $costId,
                    'big_cash' => $this->big_cash ?? false,
                    'amount' => $amount,
                    'description' => $this->description,
                    'created_by' => Auth::id(),
                ]);

                $loan->employee->increment('remaining_loan', $amount);

                activity()
                    ->performedOn($loan)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $loan->toArray(),
                    ])
                    ->log('created employee loan record');
            });

            session()->flash('success', 'Pinjaman karyawan berhasil ditambahkan.');
            return $this->redirect(route('employee-loans.index'), true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function setEmployeeId($id)
    {
        $this->employee_id = $id;
        $this->employee_search = '';
    }

    public function clearEmployee()
    {
        $this->employee_id = null;
        $this->employee_search = '';
    }

    public function render()
    {
        $employees = Employee::query()
            ->when($this->employee_search !== '', fn ($q) => $q->where('name', 'like', '%' . trim($this->employee_search) . '%'))
            ->orderBy('name')
            ->limit(50)
            ->get();

        $selectedEmployee = $this->employee_id ? Employee::find($this->employee_id) : null;
        $warehouses = Warehouse::where('has_cash', true)->orderBy('name')->get();

        return view('livewire.loan.employee.employee-loan-create', compact('employees', 'selectedEmployee', 'warehouses'));
    }
}
