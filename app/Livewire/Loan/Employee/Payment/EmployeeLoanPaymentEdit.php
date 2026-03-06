<?php

namespace App\Livewire\Loan\Employee\Payment;

use App\Models\EmployeeLoan;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Edit Pembayaran Pinjaman')]
class EmployeeLoanPaymentEdit extends Component
{
    public EmployeeLoan $employeeLoanPayment;

    public $paid_at;
    public $amount;
    public $description;
    public $big_cash;

    public function mount(EmployeeLoan $employeeLoanPayment)
    {
        if ($employeeLoanPayment->loan_type !== 'payment') {
            abort(404, 'Hanya pembayaran pinjaman yang dapat diedit dari halaman ini.');
        }

        $this->employeeLoanPayment = $employeeLoanPayment->load(['employee', 'cost']);

        $this->paid_at = $employeeLoanPayment->paid_at->format('Y-m-d');
        $this->amount = number_format($employeeLoanPayment->amount, 0);
        $this->description = $employeeLoanPayment->description;
        $this->big_cash = $employeeLoanPayment->big_cash;
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
            'paid_at.required' => 'Tanggal pembayaran harus dipilih.',
            'paid_at.date' => 'Tanggal pembayaran harus berupa tanggal.',
            'paid_at.before_or_equal' => 'Tanggal pembayaran tidak boleh lebih dari hari ini.',
            'amount.required' => 'Jumlah pembayaran harus diisi.',
            'amount.string' => 'Jumlah pembayaran harus berupa angka.',
            'description.required' => 'Deskripsi harus diisi.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ];

        $this->validate($rules, $messages);

        $newAmount = (float) Str::replace(',', '', $this->amount);
        if ($newAmount <= 0) {
            $this->addError('amount', 'Jumlah pembayaran harus lebih dari 0.');
            return;
        }

        $employee = $this->employeeLoanPayment->employee;
        $oldAmount = (float) $this->employeeLoanPayment->amount;
        $currentRemaining = (float) $employee->remaining_loan;
        $maxAllowed = $currentRemaining + $oldAmount;
        if ($newAmount > $maxAllowed) {
            $this->addError('amount', 'Jumlah pembayaran tidak boleh melebihi sisa pinjaman (maks. Rp ' . number_format($maxAllowed, 0, ',', '.') . ').');
            return;
        }

        $amountDiff = $newAmount - $oldAmount;

        try {
            DB::transaction(function () use ($newAmount, $amountDiff) {
                $record = $this->employeeLoanPayment;

                if ($amountDiff !== 0.0) {
                    $record->employee->decrement('remaining_loan', $amountDiff);
                }

                if ($record->cost_id) {
                    $record->cost->update([
                        'cost_date' => $this->paid_at,
                        'total_price' => $newAmount,
                        'description' => $this->description ?? 'Pembayaran pinjaman: ' . $record->employee->name,
                    ]);
                    $payment = $record->cost->payments()->first();
                    if ($payment) {
                        $payment->update([
                            'payment_date' => $this->paid_at,
                            'amount' => $newAmount,
                            'note' => $this->description,
                        ]);
                    }
                }

                $record->update([
                    'paid_at' => $this->paid_at,
                    'amount' => $newAmount,
                    'description' => $this->description,
                    'big_cash' => $this->big_cash ?? false,
                ]);

                activity()
                    ->performedOn($record)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $this->employeeLoanPayment->getOriginal(),
                        'attributes' => $record->fresh()->toArray(),
                    ])
                    ->log('updated employee loan payment record');
            });

            session()->flash('success', 'Pembayaran pinjaman berhasil diperbarui.');
            return $this->redirect(route('employee-loan-payments.show', $this->employeeLoanPayment), true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.loan.employee.payment.employee-loan-payment-edit');
    }
}
