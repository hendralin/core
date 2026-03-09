<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Salary;
use App\Models\SalaryDetail;
use App\Models\SalaryComponent;
use App\Models\EmployeeLoan;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Title('Edit Penggajian')]
class SalaryEdit extends Component
{
    public Salary $salary;
    public $salary_date;
    public $original_salary_date;
    /** @var array existing details: [ ['id' => x, 'salary_component_id' => x, 'component_name' => '', 'is_quantitative' => bool, 'quantity' => x, 'amount' => x, 'total_amount' => x], ... ] */
    public $details = [];
    /** @var array [ ['salary_component_id' => x, 'quantity' => x, 'amount' => x], ... ] */
    public $additionalComponents = [];

    protected ?int $pinjamanKaryawanComponentId = null;

    public function mount(Salary $salary): void
    {
        $this->salary = $salary->load([
            'salaryDetails.salaryComponent',
            'salaryDetails.vehicle.brand',
            'salaryDetails.vehicle.vehicle_model',
            'employee.employeeSalaryComponents',
        ]);
        $this->salary_date = $this->salary->salary_date?->format('Y-m-d');
        $this->original_salary_date = $this->salary->salary_date?->format('Y-m-d');
        $employee = $this->salary->employee;
        $escByScId = $employee ? $employee->employeeSalaryComponents->keyBy('salary_component_id') : collect();
        $pkId = $this->getPinjamanKaryawanComponentId();
        foreach ($this->salary->salaryDetails as $d) {
            $esc = $escByScId->get($d->salary_component_id);
            $componentName = $d->salaryComponent?->name ?? '-';
            if ($d->vehicle) {
                $vehicleLabel = trim(implode(' ', array_filter([
                    $d->vehicle->brand?->name,
                    $d->vehicle->vehicle_model?->name,
                    $d->vehicle->year,
                    $d->vehicle->police_number,
                ])));
                $componentName = $componentName . ' - ' . $vehicleLabel;
            }
            $isPk = $pkId && (int) $d->salary_component_id === (int) $pkId;
            $this->details[] = [
                'id' => $d->id,
                'salary_component_id' => $d->salary_component_id,
                'component_name' => $componentName,
                'is_quantitative' => $esc ? (bool) $esc->is_quantitative : false,
                'quantity' => $d->quantity,
                // Pinjaman Karyawan adalah potongan: tampilkan nilai absolut (input user positif)
                'amount' => number_format($isPk ? abs((float) $d->amount) : (float) $d->amount, 0, '.', ','),
                'total_amount' => $d->total_amount,
            ];
        }
    }

    protected function getPinjamanKaryawanComponentId(): ?int
    {
        if ($this->pinjamanKaryawanComponentId !== null) {
            return $this->pinjamanKaryawanComponentId;
        }
        $sc = SalaryComponent::query()
            ->whereRaw('LOWER(name) = ?', ['pinjaman karyawan'])
            ->first();
        $this->pinjamanKaryawanComponentId = $sc?->id ?? null;
        return $this->pinjamanKaryawanComponentId;
    }

    /**
     * Parse amount from form input (supports comma/thousand separators).
     */
    protected function parseAmount($raw): float
    {
        $s = preg_replace('/[^0-9.]/', '', (string) ($raw ?? 0));
        $s = str_replace('.', '', $s);
        return $s !== '' ? (float) $s : 0.0;
    }

    public function updatedDetails($value, $key)
    {
        if (str_contains($key, 'total_amount')) {
            return;
        }
        if (str_contains($key, 'quantity') || str_contains($key, 'amount')) {
            $idx = (int) explode('.', $key)[0];
            if (isset($this->details[$idx])) {
                $qty = (int) ($this->details[$idx]['quantity'] ?? 0);
                $amt = $this->parseAmount($this->details[$idx]['amount'] ?? 0);
                $pkId = $this->getPinjamanKaryawanComponentId();
                $isPk = $pkId && (int) ($this->details[$idx]['salary_component_id'] ?? 0) === (int) $pkId;
                $total = round($qty * $amt, 2);
                $this->details[$idx]['total_amount'] = $isPk ? (-1 * $total) : $total;
            }
        }
    }

    public function addAdditionalComponent(): void
    {
        $this->additionalComponents[] = [
            'salary_component_id' => '',
            'quantity' => 1,
            'amount' => 0,
        ];
    }

    public function removeAdditionalComponent(int $index): void
    {
        if (isset($this->additionalComponents[$index])) {
            array_splice($this->additionalComponents, $index, 1);
        }
    }

    public function removeDetail(int $index): void
    {
        if (isset($this->details[$index])) {
            $id = $this->details[$index]['id'] ?? null;
            if ($id) {
                SalaryDetail::where('id', $id)->delete();
            }
            array_splice($this->details, $index, 1);
        }
    }

    public function submit()
    {
        $pkId = $this->getPinjamanKaryawanComponentId();
        $employee = $this->salary->employee()->first();
        $salaryDate = Carbon::parse($this->salary_date);

        // Total potongan pinjaman lama (nilai absolut) berdasarkan record tersimpan
        $oldPkAbs = 0.0;
        if ($pkId) {
            $oldPkAbs = (float) $this->salary->salaryDetails()
                ->where('salary_component_id', $pkId)
                ->get()
                ->sum(fn ($d) => abs((float) $d->total_amount));
        }
        $currentRemaining = (float) ($employee?->remaining_loan ?? 0);
        $maxAllowedPk = $currentRemaining + $oldPkAbs;

        // Hitung total potongan pinjaman baru dari input (nilai absolut)
        $newPkAbs = 0.0;
        if ($pkId) {
            foreach ($this->details as $idx => $d) {
                if ((int) ($d['salary_component_id'] ?? 0) === (int) $pkId) {
                    $amt = abs($this->parseAmount($d['amount'] ?? 0));
                    $qty = (int) ($d['quantity'] ?? 1);
                    $newPkAbs += ($qty > 0 ? $qty : 1) * $amt;
                }
            }
            foreach ($this->additionalComponents as $idx => $add) {
                if ((int) ($add['salary_component_id'] ?? 0) === (int) $pkId) {
                    $amt = abs($this->parseAmount($add['amount'] ?? 0));
                    $qty = (int) ($add['quantity'] ?? 1);
                    $newPkAbs += ($qty > 0 ? $qty : 1) * $amt;
                }
            }
            if ($newPkAbs > $maxAllowedPk) {
                $this->addError('additionalComponents', 'Total potongan pinjaman tidak boleh melebihi sisa pinjaman (Rp ' . number_format($maxAllowedPk, 0, ',', '.') . ').');
                return;
            }
        }

        $this->validate([
            'salary_date' => 'required|date',
            'details' => 'array',
            'details.*.quantity' => 'required|integer|min:0',
            'details.*.amount' => 'required',
            'details.*.total_amount' => 'required|numeric',
            'additionalComponents' => 'array',
            'additionalComponents.*.salary_component_id' => 'required_with:additionalComponents.*|nullable|exists:salary_components,id',
            'additionalComponents.*.quantity' => 'nullable|integer|min:0',
            'additionalComponents.*.amount' => 'nullable',
        ], [
            'salary_date.required' => 'Tanggal gaji wajib diisi.',
            'details.*.quantity.required' => 'Quantity wajib diisi.',
            'details.*.amount.required' => 'Amount wajib diisi.',
        ]);

        $oldValues = [
            'salary_date' => $this->salary->salary_date?->format('Y-m-d'),
        ];

        // Ambil konfigurasi komponen gaji karyawan untuk kebutuhan is_quantitative di tambahan
        $employee = $this->salary->employee()
            ->with('employeeSalaryComponents')
            ->first();

        DB::transaction(function () use ($oldValues, $employee, $pkId, $oldPkAbs, $newPkAbs, $salaryDate) {
            $oldDate = $this->original_salary_date ? Carbon::parse($this->original_salary_date) : null;

            $this->salary->update(['salary_date' => $salaryDate]);

            foreach ($this->details as $d) {
                $quantity = (int) ($d['quantity'] ?? 0);
                if (!($d['is_quantitative'] ?? false)) {
                    $quantity = 1;
                }
                $amount = $this->parseAmount($d['amount'] ?? 0);
                $isPk = $pkId && (int) ($d['salary_component_id'] ?? 0) === (int) $pkId;
                if ($isPk) {
                    $amount = -1 * abs($amount);
                    $quantity = 1;
                }
                $totalAmount = $quantity * $amount;
                SalaryDetail::where('id', $d['id'])->update([
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'total_amount' => $totalAmount,
                ]);
            }

            foreach ($this->additionalComponents as $add) {
                $scId = (int) ($add['salary_component_id'] ?? 0);
                if ($scId <= 0) {
                    continue;
                }
                $quantity = (int) ($add['quantity'] ?? 1);
                if ($employee) {
                    $baseEsc = $employee->employeeSalaryComponents->firstWhere('salary_component_id', $scId);
                    if ($baseEsc && !$baseEsc->is_quantitative) {
                        $quantity = 1;
                    }
                }
                $amount = $this->parseAmount($add['amount'] ?? 0);
                if ($pkId && $scId === (int) $pkId) {
                    $amount = -1 * abs($amount);
                    $quantity = 1;
                }
                $totalAmount = $quantity * $amount;
                SalaryDetail::create([
                    'salary_id' => $this->salary->id,
                    'salary_component_id' => $scId,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'total_amount' => $totalAmount,
                ]);
            }

            // Sesuaikan remaining_loan berdasarkan selisih potongan pinjaman
            if ($pkId && $employee) {
                $diff = (float) $newPkAbs - (float) $oldPkAbs;
                if ($diff > 0) {
                    $employee->decrement('remaining_loan', $diff);
                } elseif ($diff < 0) {
                    $employee->increment('remaining_loan', abs($diff));
                }

                // Sinkronkan record EmployeeLoan (salary deduction): hapus yang lama lalu buat ulang sesuai total terbaru
                $q = EmployeeLoan::query()
                    ->where('employee_id', $employee->id)
                    ->where('loan_type', 'payment')
                    ->where('big_cash', true)
                    ->where('description', 'like', 'Potongan pinjaman dari gaji periode%');
                if ($oldDate) {
                    $q->whereDate('paid_at', $oldDate->toDateString());
                }
                $q->orWhere(function ($qq) use ($employee, $salaryDate) {
                    $qq->where('employee_id', $employee->id)
                        ->where('loan_type', 'payment')
                        ->where('big_cash', true)
                        ->where('description', 'like', 'Potongan pinjaman dari gaji periode%')
                        ->whereDate('paid_at', $salaryDate->toDateString());
                })->delete();

                if ($newPkAbs > 0) {
                    EmployeeLoan::create([
                        'loan_type' => 'payment',
                        'employee_id' => $employee->id,
                        'paid_at' => $salaryDate,
                        'cost_id' => null,
                        'big_cash' => true,
                        'amount' => (float) $newPkAbs,
                        'description' => 'Potongan pinjaman dari gaji periode ' . $salaryDate->format('m/Y'),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            activity()
                ->performedOn($this->salary)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => [
                        'salary_date' => $this->salary_date,
                    ],
                ])
                ->log('updated salary');
        });

        session()->flash('success', 'Data gaji berhasil diperbarui.');
        return $this->redirect(route('salaries.show', $this->salary), true);
    }

    public function render()
    {
        $salaryComponents = SalaryComponent::orderBy('name')->get();
        $pinjamanKaryawanComponentId = $this->getPinjamanKaryawanComponentId();
        return view('livewire.salary.salary-edit', compact('salaryComponents', 'pinjamanKaryawanComponentId'));
    }
}
