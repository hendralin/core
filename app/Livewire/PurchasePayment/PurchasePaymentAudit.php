<?php

namespace App\Livewire\PurchasePayment;

use Livewire\Component;
use App\Models\Activity;
use App\Models\PurchasePayment;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Purchase Payment Audit Trail')]
class PurchasePaymentAudit extends Component
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
            ->where('subject_type', PurchasePayment::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Query the purchase_payments table for payment_number, description or vehicle info
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM purchase_payments pp LEFT JOIN vehicles v ON pp.vehicle_id = v.id WHERE pp.id = activity_log.subject_id AND (pp.payment_number LIKE ? OR pp.description LIKE ? OR v.police_number LIKE ?))", ['%' . $this->search . '%', '%' . $this->search . '%', '%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicle, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM purchase_payments WHERE purchase_payments.id = activity_log.subject_id AND purchase_payments.vehicle_id = ?)", [$this->selectedVehicle]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', PurchasePayment::class)->count(),
            'today_activities' => Activity::where('subject_type', PurchasePayment::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', PurchasePayment::class)
                ->where('description', 'created purchase payment')->count(),
            'updated_count' => Activity::where('subject_type', PurchasePayment::class)
                ->where('description', 'updated purchase payment')->count(),
            'deleted_count' => Activity::where('subject_type', PurchasePayment::class)
                ->where('description', 'deleted purchase payment')->count(),
        ];

        return view('livewire.purchase-payment.purchase-payment-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
