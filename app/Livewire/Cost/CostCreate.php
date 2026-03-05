<?php

namespace App\Livewire\Cost;

use App\Models\Cost;
use App\Models\Payment;
use App\Models\Vendor;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Tambah Pembukuan Modal')]
class CostCreate extends Component
{
    use WithFileUploads;

    public $cost_type;
    public $vehicle_id;
    public $vehicle_search = '';
    public $cost_date;
    public $vendor_id;
    public $vendor_search = '';
    public $description;
    public $total_price;
    public $document;
    public $big_cash;
    public $payment_date;

    public function mount()
    {
        // Set default cost date to today
        $this->cost_date = now()->format('Y-m-d');
        // Set default big_cash to false
        $this->big_cash = false;
    }

    public function submit()
    {
        $rules = [
            'cost_type' => 'required|in:service_parts,other_cost',
            'vehicle_id' => 'required|exists:vehicles,id',
            'cost_date' => 'required|date|before_or_equal:today',
            'description' => 'required|string',
            'total_price' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'big_cash' => 'nullable|boolean',
            'payment_date' => 'nullable|date',
        ];

        $messages = [
            'cost_type.required' => 'Tipe pembukuan modal harus dipilih.',
            'cost_type.in' => 'Tipe pembukuan modal harus berupa Service & Parts atau Biaya Lainnya.',
            'vehicle_id.required' => 'Kendaraan harus dipilih.',
            'vehicle_id.exists' => 'Kendaraan tidak ditemukan.',
            'cost_date.required' => 'Tanggal pembukuan modal harus dipilih.',
            'cost_date.date' => 'Tanggal pembukuan modal harus berupa tanggal.',
            'cost_date.before_or_equal' => 'Tanggal pembukuan modal tidak boleh lebih dari hari ini.',
            'description.required' => 'Deskripsi biaya harus diisi.',
            'description.string' => 'Deskripsi biaya harus berupa teks.',
            'total_price.required' => 'Total biaya harus diisi.',
            'total_price.string' => 'Total biaya harus berupa angka.',
            'document.file' => 'Dokumen harus berupa file.',
            'document.mimes' => 'Dokumen harus berupa PDF, JPG, JPEG, atau PNG.',
            'document.max' => 'Dokumen maksimal ukuran 5MB.',
            'payment_date.date' => 'Tanggal pembayaran harus berupa tanggal.',
        ];

        if ($this->cost_type === 'service_parts') {
            $rules['vendor_id'] = 'required|exists:vendors,id';
            $messages['vendor_id.required'] = 'Vendor harus dipilih jika tipe Pembukuan Modal adalah Service & Parts.';
        }

        $this->validate($rules, $messages);

        $documentPath = null;
        if ($this->document) {
            $storedPath = $this->document->store('photos/costs', 'public');
            $documentPath = basename($storedPath);
        }

        // Parse formatted number back to numeric
        $totalPrice = Str::replace(',', '', $this->total_price);

        $vehicle = Vehicle::findOrFail($this->vehicle_id);

        // Compare as string so it works when DB returns int or string (e.g. different PHP/PDO)
        $warehouseId = in_array((string) $vehicle->warehouse_id, ['1', '3', '4', '5'], true)
            ? 4
            : (int) $vehicle->warehouse_id;

        $cost = Cost::create([
            'cost_type' => $this->cost_type,
            'vehicle_id' => $this->vehicle_id,
            'warehouse_id' => $warehouseId,
            'cost_date' => $this->cost_date,
            'vendor_id' => $this->cost_type === 'service_parts' ? $this->vendor_id : null,
            'description' => $this->description,
            'total_price' => $totalPrice,
            'document' => $documentPath,
            'big_cash' => $this->big_cash ?? false,
            'created_by' => Auth::id(),
        ]);

        if ($this->payment_date) {
            $cost->payments()->create([
                'payment_date' => $this->payment_date,
                'amount' => $totalPrice,
                'note' => null,
            ]);
        }

        // Log the creation activity
        activity()
            ->performedOn($cost)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'cost_type' => $this->cost_type,
                    'vehicle_id' => $this->vehicle_id,
                    'cost_date' => $this->cost_date,
                    'vendor_id' => $this->cost_type === 'service_parts' ? $this->vendor_id : null,
                    'description' => $this->description,
                    'total_price' => $totalPrice,
                    'document' => $documentPath,
                    'big_cash' => $this->big_cash ?? false,
                ]
            ])
            ->log('created cost record');

        session()->flash('success', 'Pembukuan modal berhasil ditambahkan.');

        return $this->redirect('/costs', true);
    }

    public function removeDocument()
    {
        $this->document = null;
    }

    public function setVehicleId($id)
    {
        $this->vehicle_id = $id;
        $this->vehicle_search = '';
    }

    public function clearVehicle()
    {
        $this->vehicle_id = null;
        $this->vehicle_search = '';
    }

    public function setVendorId($id)
    {
        $this->vendor_id = $id;
        $this->vendor_search = '';
    }

    public function clearVendor()
    {
        $this->vendor_id = null;
        $this->vendor_search = '';
    }

    public function render()
    {
        $vehicles = Vehicle::with(['brand', 'type', 'vehicle_model'])
            ->where('status', '1')
            ->when($this->vehicle_search !== '', function ($query) {
                $term = '%' . trim($this->vehicle_search) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('police_number', 'like', $term)
                        ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', $term))
                        ->orWhereHas('type', fn ($t) => $t->where('name', 'like', $term))
                        ->orWhereHas('vehicle_model', fn ($m) => $m->where('name', 'like', $term));
                });
            })
            ->orderBy('police_number')
            ->limit(50)
            ->get();

        $selectedVehicle = $this->vehicle_id
            ? Vehicle::with(['brand', 'type'])->find($this->vehicle_id)
            : null;

        $vendors = Vendor::query()
            ->when($this->vendor_search !== '', fn ($query) => $query->where('name', 'like', '%' . trim($this->vendor_search) . '%'))
            ->orderBy('name')
            ->limit(50)
            ->get();

        $selectedVendor = $this->vendor_id ? Vendor::find($this->vendor_id) : null;

        return view('livewire.cost.cost-create', compact('vehicles', 'vendors', 'selectedVehicle', 'selectedVendor'));
    }
}
