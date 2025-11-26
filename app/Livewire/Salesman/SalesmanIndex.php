<?php

namespace App\Livewire\Salesman;

use Livewire\Component;
use App\Models\Salesman;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\SalesmanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Salesmen')]
class SalesmanIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $salesmanIdToDelete = null;
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

    public function setSalesmanToDelete($salesmanId)
    {
        $this->salesmanIdToDelete = $salesmanId;
    }

    public function delete()
    {
        try {
            if (!$this->salesmanIdToDelete) {
                session()->flash('error', 'No salesman selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $salesman = Salesman::findOrFail($this->salesmanIdToDelete);

                // Store salesman data for logging before deletion
                $salesmanData = [
                    'name' => $salesman->name,
                    'phone' => $salesman->phone,
                    'email' => $salesman->email,
                    'address' => $salesman->address,
                    'user_id' => $salesman->user_id,
                ];

                $salesman->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($salesman)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $salesmanData
                    ])
                    ->log('deleted salesman');
            });

            $this->reset(['salesmanIdToDelete']);

            session()->flash('success', 'Salesman deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Salesman not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $salesmen = Salesman::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.salesman.salesman-index', compact('salesmen'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new SalesmanExport($this->search, $this->sortField, $this->sortDirection),
            'salesmen_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $salesmen = Salesman::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.salesmen-pdf', compact('salesmen'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'salesmen_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
