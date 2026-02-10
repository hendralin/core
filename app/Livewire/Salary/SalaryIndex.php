<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Salary;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;

#[Title('Penggajian')]
class SalaryIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $salaryIdToDelete = null;
    public $search = '';
    public $filterMonth = '';
    public $filterYear = '';
    public $filterEmployee = '';
    public $sortField = 'salary_date';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage', 'filterMonth', 'filterYear', 'filterEmployee'])) {
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

    public function setSalaryToDelete($salaryId)
    {
        $this->salaryIdToDelete = $salaryId;
    }

    public function delete()
    {
        try {
            if (!$this->salaryIdToDelete) {
                session()->flash('error', 'Tidak ada data gaji yang dipilih.');
                return;
            }

            DB::transaction(function () {
                $salary = Salary::with('salaryDetails')->findOrFail($this->salaryIdToDelete);
                $salaryData = [
                    'employee_id' => $salary->employee_id,
                    'salary_date' => $salary->salary_date?->format('Y-m-d'),
                ];
                $salary->salaryDetails()->delete();
                $salary->delete();
                activity()
                    ->performedOn($salary)
                    ->causedBy(Auth::user())
                    ->withProperties(['attributes' => $salaryData])
                    ->log('deleted salary');
            });

            $this->reset(['salaryIdToDelete']);
            session()->flash('success', 'Data gaji berhasil dihapus.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Data gaji tidak ditemukan.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $employees = Employee::orderBy('name')->get(['id', 'name']);

        $salaries = Salary::query()
            ->with(['employee.position', 'salaryDetails.salaryComponent'])
            ->withSum('salaryDetails as total_salary', 'total_amount')
            ->when($this->search, function ($q) {
                $q->whereHas('employee', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterMonth, function ($q) {
                $q->whereMonth('salary_date', $this->filterMonth);
            })
            ->when($this->filterYear, function ($q) {
                $q->whereYear('salary_date', $this->filterYear);
            })
            ->when($this->filterEmployee, function ($q) {
                $q->where('employee_id', $this->filterEmployee);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.salary.salary-index', compact('salaries', 'employees'));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }

    public function getMonthOptionsProperty()
    {
        return [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];
    }

    public function getYearOptionsProperty()
    {
        $current = (int) date('Y');
        $years = [];
        for ($y = $current; $y >= $current - 5; $y--) {
            $years[$y] = (string) $y;
        }
        return $years;
    }
}
