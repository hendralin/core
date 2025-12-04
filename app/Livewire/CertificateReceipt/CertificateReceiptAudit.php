<?php

namespace App\Livewire\CertificateReceipt;

use Livewire\Component;
use App\Models\Activity;
use App\Models\VehicleCertificateReceipt;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Certificate Receipt Audit Trail')]
class CertificateReceiptAudit extends Component
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
            ->where('subject_type', VehicleCertificateReceipt::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Query the vehicle_certificate_receipts table for certificate_receipt_number, in_the_name_of or vehicle info
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM vehicle_certificate_receipts vcr LEFT JOIN vehicles v ON vcr.vehicle_id = v.id WHERE vcr.id = activity_log.subject_id AND (vcr.certificate_receipt_number LIKE ? OR vcr.in_the_name_of LIKE ? OR v.police_number LIKE ?))", ['%' . $this->search . '%', '%' . $this->search . '%', '%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicle, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM vehicle_certificate_receipts WHERE vehicle_certificate_receipts.id = activity_log.subject_id AND vehicle_certificate_receipts.vehicle_id = ?)", [$this->selectedVehicle]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', VehicleCertificateReceipt::class)->count(),
            'today_activities' => Activity::where('subject_type', VehicleCertificateReceipt::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', VehicleCertificateReceipt::class)
                ->where('description', 'created vehicle certificate receipt')->count(),
            'updated_count' => Activity::where('subject_type', VehicleCertificateReceipt::class)
                ->where('description', 'updated vehicle certificate receipt')->count(),
            'deleted_count' => Activity::where('subject_type', VehicleCertificateReceipt::class)
                ->where('description', 'deleted vehicle certificate receipt')->count(),
        ];

        return view('livewire.certificate-receipt.certificate-receipt-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
