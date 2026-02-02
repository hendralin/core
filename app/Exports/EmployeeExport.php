<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EmployeeExport implements FromView
{
    protected $search;
    protected $sortField;
    protected $sortDirection;

    public function __construct($search = '', $sortField = 'name', $sortDirection = 'asc')
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
    }

    public function view(): View
    {
        $employees = Employee::query()
            ->with(['position', 'user'])
            ->withCount(['employeeSalaryComponents', 'salaries'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('join_date', 'like', '%' . $this->search . '%')
                        ->orWhereHas('position', function($positionQuery) {
                            $positionQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('user', function($userQuery) {
                            $userQuery->where('email', 'like', '%' . $this->search . '%');
                        });
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return view('exports.employees', [
            'employees' => $employees,
        ]);
    }
}
