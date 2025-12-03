<?php

namespace App\Livewire\PaymentReceipt;

use Livewire\Component;
use App\Models\Activity;
use App\Models\PaymentReceipt;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Payment Receipt Audit Trail')]
class PaymentReceiptAudit extends Component
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
            ->where('subject_type', PaymentReceipt::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Query the payment_receipts table for payment_number, description or vehicle info
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM payment_receipts pr LEFT JOIN vehicles v ON pr.vehicle_id = v.id WHERE pr.id = activity_log.subject_id AND (pr.payment_number LIKE ? OR pr.description LIKE ? OR v.police_number LIKE ?))", ['%' . $this->search . '%', '%' . $this->search . '%', '%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicle, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM payment_receipts WHERE payment_receipts.id = activity_log.subject_id AND payment_receipts.vehicle_id = ?)", [$this->selectedVehicle]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', PaymentReceipt::class)->count(),
            'today_activities' => Activity::where('subject_type', PaymentReceipt::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', PaymentReceipt::class)
                ->where('description', 'created payment receipt')->count(),
            'updated_count' => Activity::where('subject_type', PaymentReceipt::class)
                ->where('description', 'updated payment receipt')->count(),
            'deleted_count' => Activity::where('subject_type', PaymentReceipt::class)
                ->where('description', 'deleted payment receipt')->count(),
        ];

        return view('livewire.payment-receipt.payment-receipt-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
