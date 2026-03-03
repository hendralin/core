<?php

namespace App\Livewire\VehicleTaxPayment;

use App\Models\Cost;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Tambah Pembayaran PKB')]
class VehicleTaxPaymentCreate extends Component
{
    use WithFileUploads;

    public $cost_type = 'vehicle_tax';
    public $warehouse_id; // sumber Kas Pajak (warehouse)
    public $vehicle_id;
    public $cost_date;
    public $description;
    public $total_price;
    public $document;

    public function mount()
    {
        $this->cost_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $tax_cashs = Cost::query()
            ->where('cost_type', 'tax_cash')
            ->whereNull('vehicle_id')
            ->whereNull('vendor_id')
            ->whereNotNull('warehouse_id')
            ->select('warehouse_id')
            ->distinct()
            ->with('warehouse')
            ->orderBy('warehouse_id')
            ->get();

        $vehicles = Vehicle::with(['brand', 'type'])
            ->where('status', '1')
            ->orderBy('police_number')
            ->get();

        return view('livewire.vehicle-tax-payment.vehicle-tax-payment-create', compact('vehicles', 'tax_cashs'));
    }

    public function submit()
    {
        $rules = [
            'warehouse_id' => 'required|exists:warehouses,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'cost_date' => 'required|date|before_or_equal:today',
            'description' => 'required|string',
            'total_price' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        $messages = [
            'warehouse_id.required' => 'Kas Pajak (Warehouse) harus dipilih.',
            'warehouse_id.exists' => 'Kas Pajak (Warehouse) yang dipilih tidak valid.',
            'vehicle_id.exists' => 'Kendaraan yang dipilih tidak valid.',
            'cost_date.required' => 'Tanggal pembayaran PKB harus dipilih.',
            'cost_date.date' => 'Tanggal pembayaran PKB harus berupa tanggal.',
            'cost_date.before_or_equal' => 'Tanggal pembayaran PKB tidak boleh lebih dari hari ini.',
            'description.required' => 'Deskripsi pembayaran PKB harus diisi.',
            'description.string' => 'Deskripsi pembayaran PKB harus berupa teks.',
            'total_price.required' => 'Total pembayaran PKB harus diisi.',
            'total_price.string' => 'Total pembayaran PKB harus berupa angka.',
            'document.file' => 'Dokumen harus berupa file.',
            'document.mimes' => 'Dokumen harus berupa PDF, JPG, JPEG, atau PNG.',
            'document.max' => 'Dokumen maksimal ukuran 5MB.',
        ];

        $this->validate($rules, $messages);

        $documentPath = null;
        if ($this->document) {
            $storedPath = $this->document->store('photos/costs', 'public');
            $documentPath = basename($storedPath);
        }

        $totalPrice = Str::replace(',', '', $this->total_price);

        if ($this->vehicle_id) {
            Vehicle::findOrFail($this->vehicle_id);
        }

        $cost = Cost::create([
            'cost_type' => 'vehicle_tax',
            'vehicle_id' => $this->vehicle_id ?: null,
            'warehouse_id' => $this->warehouse_id,
            'cost_date' => $this->cost_date,
            'vendor_id' => null,
            'description' => $this->description,
            'total_price' => $totalPrice,
            'document' => $documentPath,
            'created_by' => Auth::id(),
        ]);

        $cost->payments()->create([
            'payment_date' => $this->cost_date,
            'amount' => $totalPrice,
            'note' => null,
        ]);

        activity()
            ->performedOn($cost)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'cost_type' => 'vehicle_tax',
                    'vehicle_id' => $this->vehicle_id,
                    'warehouse_id' => $this->warehouse_id,
                    'cost_date' => $this->cost_date,
                    'description' => $this->description,
                    'total_price' => $totalPrice,
                    'document' => $documentPath,
                ]
            ])
            ->log('created pembayaran pkb record');

        session()->flash('success', 'Pembayaran PKB berhasil ditambahkan.');

        return $this->redirect('/vehicle-tax-payments', true);
    }

    public function removeDocument()
    {
        $this->document = null;
    }
}
