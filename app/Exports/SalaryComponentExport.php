<?php

namespace App\Exports;

use App\Models\SalaryComponent;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalaryComponentExport implements FromView
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
        $salaryComponents = SalaryComponent::query()
            ->withCount('employeeSalaryComponents')
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return view('exports.salary-components', [
            'salaryComponents' => $salaryComponents,
        ]);
    }
}
