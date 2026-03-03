<?php

namespace App\Livewire\VehicleTaxPayment;

use App\Models\Cost;
use App\Models\Vehicle;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Title('Edit Pembayaran PKB')]
class VehicleTaxPaymentEdit extends Component
{
    use WithFileUploads;

    public Cost $cost;

    public $cost_type;
    public $warehouse_id; // sumber Kas Pajak (warehouse)
    public $vehicle_id;
    public $cost_date;
    public $description;
    public $total_price;
    public $document;
    public $existing_document;

    public function mount(Cost $vehicleTaxPayment)
    {
        // Route model binding uses {vehicleTaxPayment}; keep variable name aligned with routes.
        $this->cost = $vehicleTaxPayment;

        if ($this->cost->status !== 'pending') {
            abort(403, 'Tidak dapat mengubah pembayaran PKB yang telah disetujui atau ditolak.');
        }

        if ($this->cost->cost_type !== 'vehicle_tax') {
            abort(403, 'Record ini bukan merupakan pembayaran PKB.');
        }

        $this->cost_type = $this->cost->cost_type;
        $this->warehouse_id = $this->cost->warehouse_id;
        $this->vehicle_id = $this->cost->vehicle_id;
        $this->cost_date = Carbon::parse($this->cost->cost_date)->format('Y-m-d');
        $this->description = $this->cost->description;
        $this->total_price = number_format($this->cost->total_price, 0);
        $this->existing_document = $this->cost->document;
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

        return view('livewire.vehicle-tax-payment.vehicle-tax-payment-edit', compact('vehicles', 'tax_cashs'));
    }

    public function submit()
    {
        $rules = [
            'warehouse_id' => 'required|exists:warehouses,id',
            'vehicle_id' => 'required|exists:vehicles,id',
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

        $documentPath = $this->existing_document;
        if ($this->document) {
            if ($this->existing_document) {
                Storage::disk('public')->delete('photos/costs/' . $this->existing_document);
            }
            $storedPath = $this->document->store('photos/costs', 'public');
            $documentPath = basename($storedPath);
            $this->existing_document = $documentPath;
            $this->document = null;
        }

        $totalPrice = Str::replace(',', '', $this->total_price);

        if ($this->vehicle_id) {
            Vehicle::findOrFail($this->vehicle_id);
        }

        $oldValues = [
            'vehicle_id' => $this->cost->getOriginal('vehicle_id'),
            'warehouse_id' => $this->cost->getOriginal('warehouse_id'),
            'cost_date' => $this->cost->getOriginal('cost_date'),
            'description' => $this->cost->getOriginal('description'),
            'total_price' => $this->cost->getOriginal('total_price'),
            'document' => $this->cost->getOriginal('document'),
        ];

        $this->cost->update([
            'cost_type' => 'vehicle_tax',
            'vehicle_id' => $this->vehicle_id ?: null,
            'warehouse_id' => $this->warehouse_id,
            'cost_date' => $this->cost_date,
            'vendor_id' => null,
            'description' => $this->description,
            'total_price' => $totalPrice,
            'document' => $documentPath,
        ]);

        if ($totalPrice != $oldValues['total_price'] || $this->cost_date !== Carbon::parse($oldValues['cost_date'])->format('Y-m-d')) {
            $firstPayment = $this->cost->payments()->first();
            if ($firstPayment) {
                $firstPayment->update([
                    'payment_date' => $this->cost_date,
                    'amount' => $totalPrice,
                ]);
            } else {
                $this->cost->payments()->create([
                    'payment_date' => $this->cost_date,
                    'amount' => $totalPrice,
                    'note' => null,
                ]);
            }
        }

        activity()
            ->performedOn($this->cost)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'vehicle_id' => $this->vehicle_id,
                    'warehouse_id' => $this->warehouse_id,
                    'cost_date' => $this->cost_date,
                    'description' => $this->description,
                    'total_price' => $totalPrice,
                    'document' => $documentPath,
                ]
            ])
            ->log('updated pembayaran pkb record');

        session()->flash('success', 'Pembayaran PKB berhasil diperbarui.');

        return $this->redirect('/vehicle-tax-payments', true);
    }

    public function removeDocument()
    {
        if ($this->existing_document) {
            Storage::disk('public')->delete('photos/costs/' . $this->existing_document);
            $this->existing_document = null;
        }

        $this->cost->update(['document' => null]);
    }
}
