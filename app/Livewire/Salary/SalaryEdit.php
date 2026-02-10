<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Salary;
use App\Models\SalaryDetail;
use App\Models\SalaryComponent;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Title('Edit Penggajian')]
class SalaryEdit extends Component
{
    public Salary $salary;
    public $salary_date;
    /** @var array existing details: [ ['id' => x, 'salary_component_id' => x, 'component_name' => '', 'is_quantitative' => bool, 'quantity' => x, 'amount' => x, 'total_amount' => x], ... ] */
    public $details = [];
    /** @var array [ ['salary_component_id' => x, 'quantity' => x, 'amount' => x], ... ] */
    public $additionalComponents = [];

    public function mount(Salary $salary): void
    {
        $this->salary = $salary->load([
            'salaryDetails.salaryComponent',
            'salaryDetails.vehicle.brand',
            'salaryDetails.vehicle.vehicle_model',
            'employee.employeeSalaryComponents',
        ]);
        $this->salary_date = $this->salary->salary_date?->format('Y-m-d');
        $employee = $this->salary->employee;
        $escByScId = $employee ? $employee->employeeSalaryComponents->keyBy('salary_component_id') : collect();
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
            $this->details[] = [
                'id' => $d->id,
                'salary_component_id' => $d->salary_component_id,
                'component_name' => $componentName,
                'is_quantitative' => $esc ? (bool) $esc->is_quantitative : false,
                'quantity' => $d->quantity,
                'amount' => number_format($d->amount, 0, '.', ','),
                'total_amount' => $d->total_amount,
            ];
        }
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
                $amt = (float) (preg_replace('/[^0-9.]/', '', (string) ($this->details[$idx]['amount'] ?? 0)) ?: 0);
                $this->details[$idx]['total_amount'] = round($qty * $amt, 2);
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
        $this->validate([
            'salary_date' => 'required|date',
            'details' => 'array',
            'details.*.quantity' => 'required|integer|min:0',
            'details.*.amount' => 'required',
            'details.*.total_amount' => 'required|numeric|min:0',
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

        DB::transaction(function () use ($oldValues, $employee) {
            $this->salary->update(['salary_date' => Carbon::parse($this->salary_date)]);

            foreach ($this->details as $d) {
                $quantity = (int) ($d['quantity'] ?? 0);
                if (!($d['is_quantitative'] ?? false)) {
                    $quantity = 1;
                }
                $amount = (float) (preg_replace('/[^0-9.]/', '', (string) ($d['amount'] ?? 0)) ?: 0);
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
                $amount = (float) (preg_replace('/[^0-9.]/', '', (string) ($add['amount'] ?? 0)) ?: 0);
                $totalAmount = $quantity * $amount;
                SalaryDetail::create([
                    'salary_id' => $this->salary->id,
                    'salary_component_id' => $scId,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'total_amount' => $totalAmount,
                ]);
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
        return view('livewire.salary.salary-edit', compact('salaryComponents'));
    }
}
