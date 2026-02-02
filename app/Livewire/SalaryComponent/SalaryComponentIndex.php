<?php

namespace App\Livewire\SalaryComponent;

use Livewire\Component;
use App\Models\SalaryComponent;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\SalaryComponentExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Salary Components')]
class SalaryComponentIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $salaryComponentIdToDelete = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
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

    public function setSalaryComponentToDelete($salaryComponentId)
    {
        $this->salaryComponentIdToDelete = $salaryComponentId;
    }

    public function delete()
    {
        try {
            if (!$this->salaryComponentIdToDelete) {
                session()->flash('error', 'No salary component selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $salaryComponent = SalaryComponent::findOrFail($this->salaryComponentIdToDelete);

                // Store salary component data for logging before deletion
                $salaryComponentData = [
                    'name' => $salaryComponent->name,
                    'description' => $salaryComponent->description,
                ];

                $salaryComponent->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($salaryComponent)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $salaryComponentData
                    ])
                    ->log('deleted salary component');
            });

            $this->reset(['salaryComponentIdToDelete']);

            session()->flash('success', 'Salary component deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Salary component not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
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
            ->paginate($this->perPage);

        return view('livewire.salary-component.salary-component-index', compact('salaryComponents'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new SalaryComponentExport($this->search, $this->sortField, $this->sortDirection),
            'salary_components_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
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

        $pdf = Pdf::loadView('exports.salary-components-pdf', compact('salaryComponents'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'salary_components_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
