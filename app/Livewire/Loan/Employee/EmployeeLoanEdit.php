<?php

namespace App\Livewire\Loan\Employee;

use App\Models\EmployeeLoan;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Edit Pinjaman Karyawan')]
class EmployeeLoanEdit extends Component
{
    public EmployeeLoan $employeeLoan;

    public $paid_at;
    public $amount;
    public $description;
    public $big_cash;

    public function mount(EmployeeLoan $employeeLoan)
    {
        if ($employeeLoan->loan_type !== 'loan') {
            abort(404, 'Hanya pinjaman yang dapat diedit dari halaman ini.');
        }

        $this->employeeLoan = $employeeLoan->load(['employee', 'cost']);

        $this->paid_at = $employeeLoan->paid_at->format('Y-m-d');
        $this->amount = number_format($employeeLoan->amount, 0);
        $this->description = $employeeLoan->description;
        $this->big_cash = $employeeLoan->big_cash;
    }

    public function submit()
    {
        $rules = [
            'paid_at' => 'required|date|before_or_equal:today',
            'amount' => 'required|string',
            'description' => 'required|string',
            'big_cash' => 'nullable|boolean',
        ];

        $messages = [
            'paid_at.required' => 'Tanggal pinjaman harus dipilih.',
            'paid_at.date' => 'Tanggal pinjaman harus berupa tanggal.',
            'paid_at.before_or_equal' => 'Tanggal pinjaman tidak boleh lebih dari hari ini.',
            'amount.required' => 'Jumlah pinjaman harus diisi.',
            'amount.string' => 'Jumlah pinjaman harus berupa angka.',
            'description.required' => 'Deskripsi harus diisi.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ];

        $this->validate($rules, $messages);

        $newAmount = (float) Str::replace(',', '', $this->amount);
        if ($newAmount <= 0) {
            $this->addError('amount', 'Jumlah pinjaman harus lebih dari 0.');
            return;
        }

        $oldAmount = (float) $this->employeeLoan->amount;
        $amountDiff = $newAmount - $oldAmount;

        try {
            DB::transaction(function () use ($newAmount, $amountDiff) {
                $loan = $this->employeeLoan;

                if ($amountDiff !== 0.0) {
                    $loan->employee->increment('remaining_loan', $amountDiff);
                }

                if ($loan->cost_id) {
                    $loan->cost->update([
                        'cost_date' => $this->paid_at,
                        'total_price' => $newAmount,
                        'description' => $this->description ?? 'Pinjaman karyawan: ' . $loan->employee->name,
                    ]);
                    $payment = $loan->cost->payments()->first();
                    if ($payment) {
                        $payment->update([
                            'payment_date' => $this->paid_at,
                            'amount' => $newAmount,
                            'note' => $this->description,
                        ]);
                    }
                }

                $loan->update([
                    'paid_at' => $this->paid_at,
                    'amount' => $newAmount,
                    'description' => $this->description,
                    'big_cash' => $this->big_cash ?? false,
                ]);

                activity()
                    ->performedOn($loan)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $this->employeeLoan->getOriginal(),
                        'attributes' => $loan->fresh()->toArray(),
                    ])
                    ->log('updated employee loan record');
            });

            session()->flash('success', 'Pinjaman karyawan berhasil diperbarui.');
            return $this->redirect(route('employee-loans.show', $this->employeeLoan), true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.loan.employee.employee-loan-edit');
    }
}
