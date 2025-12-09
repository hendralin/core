<?php

namespace App\Livewire\VehicleFile;

use Livewire\Component;
use App\Models\Activity;
use App\Models\VehicleFile;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Vehicle File Audit Trail')]
class VehicleFileAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedVehicle = null;
    public $vehicles = [];

    public function mount()
    {
        $this->vehicles = Vehicle::select('id', 'police_number')->orderBy('police_number')->get();

        // Check for selectedVehicle from URL parameters
        if (request()->has('selectedVehicle')) {
            $this->selectedVehicle = request()->get('selectedVehicle');
        }
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedVehicle'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedVehicle()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedVehicle']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', VehicleFile::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Query the vehicle_files table for file_path or vehicle info
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM vehicle_files vf LEFT JOIN vehicles v ON vf.vehicle_id = v.id WHERE vf.id = activity_log.subject_id AND (vf.file_path LIKE ? OR v.police_number LIKE ?))", ['%' . $this->search . '%', '%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicle, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM vehicle_files WHERE vehicle_files.id = activity_log.subject_id AND vehicle_files.vehicle_id = ?)", [$this->selectedVehicle]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', VehicleFile::class)->count(),
            'today_activities' => Activity::where('subject_type', VehicleFile::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', VehicleFile::class)
                ->where('description', 'created vehicle file')->count(),
            'updated_count' => Activity::where('subject_type', VehicleFile::class)
                ->where('description', 'updated vehicle file')->count(),
            'deleted_count' => Activity::where('subject_type', VehicleFile::class)
                ->where('description', 'deleted vehicle file')->count(),
        ];

        return view('livewire.vehicle-file.vehicle-file-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
