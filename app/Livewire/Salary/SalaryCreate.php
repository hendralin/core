<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Salesman;
use App\Models\Vehicle;
use App\Models\Salary;
use App\Models\SalaryDetail;
use App\Models\SalaryComponent;
use App\Models\EmployeeLoan;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Title('Buat Penggajian')]
class SalaryCreate extends Component
{
    public $period_month;
    public $period_year;
    public $employee_ids = [];

    /** @var array [employee_id => [esc_id => ['quantity' => int, 'amount' => float], ...], ...] */
    public $componentInputs = [];

    /** @var array [employee_id => [ ['salary_component_id' => id, 'quantity' => int, 'amount' => float], ... ], ...] */
    public $additionalComponents = [];

    protected ?int $insentifComponentId = null;

    protected ?int $pinjamanKaryawanComponentId = null;

    public function mount()
    {
        $this->period_month = now()->format('m');
        $this->period_year = now()->format('Y');
    }

    public function updatedEmployeeIds()
    {
        $this->syncComponentInputs();
    }

    public function updatedPeriodMonth()
    {
        $this->syncComponentInputs();
    }

    public function updatedPeriodYear()
    {
        $this->syncComponentInputs();
    }

    protected function syncComponentInputs(): void
    {
        $newInputs = [];
        foreach ($this->employee_ids as $eid) {
            $employee = Employee::with('employeeSalaryComponents.salaryComponent')->find($eid);
            if (!$employee) {
                continue;
            }
            if (isset($this->componentInputs[$eid])) {
                $newInputs[$eid] = $this->componentInputs[$eid];
                continue;
            }
            foreach ($employee->employeeSalaryComponents as $esc) {
                // Default: komponen non-kuantitatif qty = 1, kuantitatif = 0 (akan diisi manual)
                $qty = $esc->is_quantitative ? 0 : 1;
                $newInputs[$eid][$esc->id] = [
                    'quantity' => $qty,
                    'amount' => (float) $esc->amount,
                ];
            }
        }
        $this->componentInputs = $newInputs;

        // Auto-insert insentif per kendaraan terjual pada periode
        $this->syncAutoInsentifVehicles();
    }

    public function addAdditionalComponent($employeeId): void
    {
        $employeeId = (int) $employeeId;
        if (!isset($this->additionalComponents[$employeeId])) {
            $this->additionalComponents[$employeeId] = [];
        }
        $this->additionalComponents[$employeeId][] = [
            'salary_component_id' => '',
            'vehicle_id' => null,
            'vehicle_label' => null,
            'is_auto' => false,
            'quantity' => 1,
            'amount' => 0,
        ];
    }

    public function removeAdditionalComponent($employeeId, $index): void
    {
        $employeeId = (int) $employeeId;
        $index = (int) $index;
        if (isset($this->additionalComponents[$employeeId][$index])) {
            array_splice($this->additionalComponents[$employeeId], $index, 1);
            if (empty($this->additionalComponents[$employeeId])) {
                unset($this->additionalComponents[$employeeId]);
            }
        }
    }

    protected function getInsentifComponentId(): ?int
    {
        if ($this->insentifComponentId !== null) {
            return $this->insentifComponentId;
        }
        $sc = SalaryComponent::query()
            ->whereRaw('LOWER(name) = ?', ['insentif'])
            ->first();
        $this->insentifComponentId = $sc?->id ?? null;
        return $this->insentifComponentId;
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
     * Parse amount from form input (supports raw number or Indonesian formatted string e.g. "1.234.567").
     */
    protected function parseAmount($raw): float
    {
        $s = preg_replace('/[^0-9.]/', '', (string) ($raw ?? 0));
        $s = str_replace('.', '', $s);
        return $s !== '' ? (float) $s : 0.0;
    }

    public function syncPinjamanAmount($employeeId, $addIndex): void
    {
        $employeeId = (int) $employeeId;
        $addIndex = (int) $addIndex;
        $pkId = $this->getPinjamanKaryawanComponentId();
        if (!$pkId || !isset($this->additionalComponents[$employeeId][$addIndex])) {
            return;
        }
        $scId = (int) ($this->additionalComponents[$employeeId][$addIndex]['salary_component_id'] ?? 0);
        if ($scId === $pkId) {
            $employee = Employee::find($employeeId);
            if ($employee) {
                $remainingLoan = (float) $employee->remaining_loan;
                $this->additionalComponents[$employeeId][$addIndex]['amount'] = number_format($remainingLoan, 0, ',', ',');
                $this->additionalComponents[$employeeId][$addIndex]['quantity'] = 1;
            }
        }
    }

    protected function buildVehicleLabel(Vehicle $v): string
    {
        $parts = array_filter([
            $v->brand?->name,
            $v->vehicle_model?->name,
            $v->year,
            $v->police_number,
        ]);
        return implode(' ', $parts);
    }

    protected function syncAutoInsentifVehicles(): void
    {
        $month = (int) $this->period_month;
        $year = (int) $this->period_year;
        $selectedIds = collect($this->employee_ids)->map(fn ($id) => (int) $id)->all();
        $pkId = $this->getPinjamanKaryawanComponentId();
        $insentifId = $this->getInsentifComponentId();

        // Bersihkan data karyawan yang tidak lagi dipilih
        foreach (array_keys($this->additionalComponents) as $empId) {
            if (!in_array((int) $empId, $selectedIds, true)) {
                unset($this->additionalComponents[$empId]);
            }
        }

        foreach ($selectedIds as $empId) {
            $employee = Employee::with(['employeeSalaryComponents', 'user'])->find($empId);
            if (!$employee) {
                continue;
            }

            $existing = $this->additionalComponents[$empId] ?? [];
            $existingPinjaman = null;
            $existingAutoByVehicle = [];
            $manualRows = [];
            foreach ($existing as $r) {
                if (($r['is_auto_loan'] ?? false) || ($pkId && (int) ($r['salary_component_id'] ?? 0) === $pkId)) {
                    $existingPinjaman = $r;
                } elseif (($r['is_auto'] ?? false) && !empty($r['vehicle_id'])) {
                    $existingAutoByVehicle[(int) $r['vehicle_id']] = $r;
                } else {
                    $manualRows[] = $r;
                }
            }

            $rows = [];

            // Auto: Pinjaman Karyawan (jika ada sisa pinjaman)
            $remainingLoan = (float) ($employee->remaining_loan ?? 0);
            if ($pkId && $remainingLoan > 0) {
                $rows[] = [
                    'salary_component_id' => $pkId,
                    'vehicle_id' => null,
                    'vehicle_label' => null,
                    'is_auto' => false,
                    'is_auto_loan' => true,
                    'quantity' => 1,
                    'amount' => $existingPinjaman['amount'] ?? number_format($remainingLoan, 0, ',', ','),
                ];
            }

            // Auto: Insentif per kendaraan terjual (jika ada komponen Insentif dan karyawan adalah salesman)
            if ($insentifId && $employee->user_id) {
                $salesman = Salesman::where('user_id', $employee->user_id)->first();
                if ($salesman) {
                    $vehicles = Vehicle::query()
                        ->with(['brand', 'vehicle_model'])
                        ->where('salesman_id', $salesman->id)
                        ->where('status', '0')
                        ->whereNotNull('selling_date')
                        ->whereYear('selling_date', $year)
                        ->whereMonth('selling_date', $month)
                        ->orderBy('selling_date')
                        ->get();
                    $defaultInsentifAmount = (float) ($employee->employeeSalaryComponents->firstWhere('salary_component_id', $insentifId)?->amount ?? 0);
                    foreach ($vehicles as $v) {
                        $prev = $existingAutoByVehicle[(int) $v->id] ?? null;
                        $rows[] = [
                            'salary_component_id' => $insentifId,
                            'vehicle_id' => $v->id,
                            'vehicle_label' => 'Insentif - ' . $this->buildVehicleLabel($v),
                            'is_auto' => true,
                            'is_auto_loan' => false,
                            'quantity' => (int) ($prev['quantity'] ?? 1),
                            'amount' => $prev['amount'] ?? $defaultInsentifAmount,
                        ];
                    }
                }
            }

            $this->additionalComponents[$empId] = array_values(array_merge($rows, $manualRows));
        }
    }

    public function submit()
    {
        $this->validate([
            'period_month' => 'required|string|size:2',
            'period_year' => 'required|string|size:4',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ], [
            'period_month.required' => 'Bulan periode wajib dipilih.',
            'period_year.required' => 'Tahun periode wajib dipilih.',
            'employee_ids.required' => 'Pilih minimal satu karyawan.',
            'employee_ids.min' => 'Pilih minimal satu karyawan.',
        ]);

        $month = (int) $this->period_month;
        $year = (int) $this->period_year;
        $salaryDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $employees = Employee::whereIn('id', $this->employee_ids)->with('employeeSalaryComponents')->get();

        if ($employees->isEmpty()) {
            session()->flash('error', 'Tidak ada karyawan dengan komponen gaji. Tambah komponen gaji di data karyawan terlebih dahulu.');
            return;
        }

        $pkComponentId = $this->getPinjamanKaryawanComponentId();
        foreach ($employees as $employee) {
            $additionals = $this->additionalComponents[$employee->id] ?? [];
            foreach ($additionals as $idx => $add) {
                $scId = (int) ($add['salary_component_id'] ?? 0);
                if ($pkComponentId && $scId === $pkComponentId) {
                    $employee->refresh();
                    $remainingLoan = (float) $employee->remaining_loan;
                    $amount = $this->parseAmount($add['amount'] ?? 0);
                    if ($amount > $remainingLoan) {
                        $this->addError('additionalComponents.' . $employee->id . '.' . $idx . '.amount', 'Jumlah Pinjaman Karyawan tidak boleh melebihi sisa pinjaman (Rp ' . number_format($remainingLoan, 0, ',', '.') . ').');
                        return;
                    }
                }
            }
        }

        $created = 0;
        $skipped = [];
        $useComponentInputs = !empty($this->componentInputs);

        DB::transaction(function () use ($employees, $salaryDate, $month, $year, $useComponentInputs, $pkComponentId, &$created, &$skipped) {
            foreach ($employees as $employee) {
                $exists = Salary::where('employee_id', $employee->id)
                    ->whereYear('salary_date', $year)
                    ->whereMonth('salary_date', $month)
                    ->exists();
                if ($exists) {
                    $skipped[] = $employee->name;
                    continue;
                }

                $salary = Salary::create([
                    'employee_id' => $employee->id,
                    'salary_date' => $salaryDate,
                ]);

                $inputs = $useComponentInputs && isset($this->componentInputs[$employee->id])
                    ? $this->componentInputs[$employee->id]
                    : null;

                foreach ($employee->employeeSalaryComponents as $esc) {
                    if ($inputs && isset($inputs[$esc->id])) {
                        $quantity = (int) ($inputs[$esc->id]['quantity'] ?? 0);
                        if (!$esc->is_quantitative) {
                            $quantity = 1;
                        }
                        $amount = $this->parseAmount($inputs[$esc->id]['amount'] ?? 0);
                        $totalAmount = $quantity * $amount;
                    } else {
                        // Default tanpa input manual: non-kuantitatif = 1, kuantitatif = 0
                        $quantity = $esc->is_quantitative ? 0 : 1;
                        $amount = (float) $esc->amount;
                        $totalAmount = $quantity * $amount;
                    }

                    SalaryDetail::create([
                        'salary_id' => $salary->id,
                        'salary_component_id' => $esc->salary_component_id,
                        'vehicle_id' => null,
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'total_amount' => $totalAmount,
                    ]);
                }

                $additionals = $this->additionalComponents[$employee->id] ?? [];
                foreach ($additionals as $add) {
                    $scId = (int) ($add['salary_component_id'] ?? 0);
                    if ($scId <= 0) {
                        continue;
                    }
                    $salaryComponent = SalaryComponent::find($scId);
                    if (!$salaryComponent) {
                        continue;
                    }
                    $isPinjamanKaryawan = $pkComponentId && $scId === $pkComponentId;
                    $quantity = 1;
                    if ($isPinjamanKaryawan) {
                        $absAmount = abs($this->parseAmount($add['amount'] ?? 0));
                        if ($absAmount <= 0) {
                            continue;
                        }
                        // Pinjaman Karyawan adalah potongan (pengurang gaji): simpan nilai negatif di SalaryDetail
                        $amount = -1 * $absAmount;
                    } else {
                        $quantity = (int) ($add['quantity'] ?? 1);
                        $baseEsc = $employee->employeeSalaryComponents->firstWhere('salary_component_id', $scId);
                        if ($baseEsc && !$baseEsc->is_quantitative) {
                            $quantity = 1;
                        }
                        $amount = $this->parseAmount($add['amount'] ?? 0);
                    }
                    $totalAmount = $quantity * $amount;
                    SalaryDetail::create([
                        'salary_id' => $salary->id,
                        'salary_component_id' => $scId,
                        'vehicle_id' => !empty($add['vehicle_id']) ? (int) $add['vehicle_id'] : null,
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'total_amount' => $totalAmount,
                    ]);
                    if ($isPinjamanKaryawan) {
                        // Kurangi remaining_loan dengan nilai absolut (karena amount salary detail bernilai negatif)
                        $absAmount = abs((float) $totalAmount);
                        if ($absAmount <= 0) {
                            continue;
                        }
                        $employee->decrement('remaining_loan', $absAmount);
                        EmployeeLoan::create([
                            'loan_type' => 'payment',
                            'employee_id' => $employee->id,
                            'paid_at' => $salaryDate,
                            'cost_id' => null,
                            'big_cash' => true,
                            'amount' => $absAmount,
                            'description' => 'Potongan pinjaman dari gaji periode ' . $salaryDate->format('m/Y'),
                            'created_by' => Auth::id(),
                        ]);
                    }
                }

                activity()
                    ->performedOn($salary)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => [
                            'employee_id' => $salary->employee_id,
                            'salary_date' => $salary->salary_date->format('Y-m-d'),
                        ],
                    ])
                    ->log('created salary');
                $created++;
            }
        });

        $msg = $created . ' data gaji berhasil dibuat.';
        if (count($skipped) > 0) {
            $msg .= ' Dilewati (sudah ada gaji di periode ini): ' . implode(', ', $skipped);
        }
        session()->flash('success', $msg);
        return $this->redirect(route('salaries.index'), true);
    }

    public function render()
    {
        // Tampilkan semua karyawan yang punya minimal 1 komponen gaji (tanpa filter status)
        $employees = Employee::query()
            ->with(['position', 'employeeSalaryComponents.salaryComponent'])
            ->whereHas('employeeSalaryComponents')
            ->orderBy('name')
            ->get();

        $monthOptions = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];
        $currentYear = (int) date('Y');
        $yearOptions = [];
        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
            $yearOptions[$y] = $y;
        }

        $selectedEmployees = $employees->filter(fn ($e) => in_array($e->id, $this->employee_ids))->values();
        $salaryComponents = SalaryComponent::orderBy('name')->get();
        $pinjamanKaryawanComponentId = $this->getPinjamanKaryawanComponentId();

        return view('livewire.salary.salary-create', compact('employees', 'monthOptions', 'yearOptions', 'selectedEmployees', 'salaryComponents', 'pinjamanKaryawanComponentId'));
    }
}
