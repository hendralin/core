<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Salary;
use Livewire\Attributes\Title;

#[Title('Detail Penggajian')]
class SalaryShow extends Component
{
    public Salary $salary;

    public function mount(Salary $salary): void
    {
        $this->salary = $salary->load([
            'employee.position',
            'salaryDetails.salaryComponent',
            'salaryDetails.vehicle.brand',
            'salaryDetails.vehicle.vehicle_model',
        ]);
    }

    public function render()
    {
        $salary = $this->salary;
        $totalSalary = $salary->salaryDetails->sum('total_amount');
        return view('livewire.salary.salary-show', compact('salary', 'totalSalary'));
    }
}
