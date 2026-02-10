<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Salesman;
use App\Models\Vehicle;
use App\Models\Salary;
use App\Models\SalaryDetail;
use App\Models\SalaryComponent;
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
        $insentifId = $this->getInsentifComponentId();
        if (!$insentifId) {
            // Jika komponen Insentif belum ada, tidak ada auto insert
            return;
        }

        $month = (int) $this->period_month;
        $year = (int) $this->period_year;

        // Bersihkan data karyawan yang tidak lagi dipilih
        $selectedIds = collect($this->employee_ids)->map(fn ($id) => (int) $id)->all();
        foreach (array_keys($this->additionalComponents) as $empId) {
            if (!in_array((int) $empId, $selectedIds, true)) {
                unset($this->additionalComponents[$empId]);
            }
        }

        foreach ($selectedIds as $empId) {
            $employee = Employee::with(['employeeSalaryComponents', 'user'])->find($empId);
            if (!$employee || !$employee->user_id) {
                continue;
            }
            $salesman = Salesman::where('user_id', $employee->user_id)->first();
            if (!$salesman) {
                continue;
            }

            $vehicles = Vehicle::query()
                ->with(['brand', 'vehicle_model'])
                ->where('salesman_id', $salesman->id)
                ->where('status', '0') // 0 = sold
                ->whereNotNull('selling_date')
                ->whereYear('selling_date', $year)
                ->whereMonth('selling_date', $month)
                ->orderBy('selling_date')
                ->get();

            // amount default untuk insentif: dari konfigurasi komponen gaji karyawan (EmployeeSalaryComponent)
            $defaultInsentifAmount = (float) ($employee->employeeSalaryComponents->firstWhere('salary_component_id', $insentifId)?->amount ?? 0);

            $existing = $this->additionalComponents[$empId] ?? [];
            $manualRows = array_values(array_filter($existing, fn ($r) => !($r['is_auto'] ?? false)));
            $existingAutoByVehicle = [];
            foreach ($existing as $r) {
                if (($r['is_auto'] ?? false) && !empty($r['vehicle_id'])) {
                    $existingAutoByVehicle[(int) $r['vehicle_id']] = $r;
                }
            }

            $autoRows = [];
            foreach ($vehicles as $v) {
                $prev = $existingAutoByVehicle[(int) $v->id] ?? null;
                $autoRows[] = [
                    'salary_component_id' => $insentifId,
                    'vehicle_id' => $v->id,
                    'vehicle_label' => 'Insentif - ' . $this->buildVehicleLabel($v),
                    'is_auto' => true,
                    'quantity' => (int) ($prev['quantity'] ?? 1),
                    'amount' => $prev['amount'] ?? $defaultInsentifAmount,
                ];
            }

            // Auto rows tampil duluan, manual tetap dipertahankan
            $this->additionalComponents[$empId] = array_values(array_merge($autoRows, $manualRows));
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

        $created = 0;
        $skipped = [];
        $useComponentInputs = !empty($this->componentInputs);

        DB::transaction(function () use ($employees, $salaryDate, $month, $year, $useComponentInputs, &$created, &$skipped) {
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
                        $amount = (float) (preg_replace('/[^0-9.]/', '', (string) ($inputs[$esc->id]['amount'] ?? 0)) ?: 0);
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
                    $quantity = (int) ($add['quantity'] ?? 1);
                    // Jika komponen ini non-kuantitatif di konfigurasi karyawan, paksa qty = 1
                    $baseEsc = $employee->employeeSalaryComponents->firstWhere('salary_component_id', $scId);
                    if ($baseEsc && !$baseEsc->is_quantitative) {
                        $quantity = 1;
                    }
                    $amount = (float) (preg_replace('/[^0-9.]/', '', (string) ($add['amount'] ?? 0)) ?: 0);
                    $totalAmount = $quantity * $amount;
                    SalaryDetail::create([
                        'salary_id' => $salary->id,
                        'salary_component_id' => $scId,
                        'vehicle_id' => !empty($add['vehicle_id']) ? (int) $add['vehicle_id'] : null,
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'total_amount' => $totalAmount,
                    ]);
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

        return view('livewire.salary.salary-create', compact('employees', 'monthOptions', 'yearOptions', 'selectedEmployees', 'salaryComponents'));
    }
}
