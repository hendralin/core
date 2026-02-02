<?php

namespace App\Livewire\Position;

use Livewire\Component;
use App\Models\Position;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\PositionExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Positions')]
class PositionIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $positionIdToDelete = null;
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

    public function setPositionToDelete($positionId)
    {
        $this->positionIdToDelete = $positionId;
    }

    public function delete()
    {
        try {
            if (!$this->positionIdToDelete) {
                session()->flash('error', 'No position selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $position = Position::findOrFail($this->positionIdToDelete);

                // Store position data for logging before deletion
                $positionData = [
                    'name' => $position->name,
                    'description' => $position->description,
                ];

                $position->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($position)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $positionData
                    ])
                    ->log('deleted position');
            });

            $this->reset(['positionIdToDelete']);

            session()->flash('success', 'Position deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Position not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $positions = Position::query()
            ->withCount('employees')
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

        return view('livewire.position.position-index', compact('positions'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new PositionExport($this->search, $this->sortField, $this->sortDirection),
            'positions_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $positions = Position::query()
            ->withCount('employees')
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

        $pdf = Pdf::loadView('exports.positions-pdf', compact('positions'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'positions_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
