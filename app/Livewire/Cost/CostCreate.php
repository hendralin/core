<?php

namespace App\Livewire\Cost;

use App\Models\Cost;
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
    public $cost_date;
    public $vendor_id;
    public $description;
    public $total_price;
    public $document;
    public $big_cash;

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

        $cost = Cost::create([
            'cost_type' => $this->cost_type,
            'vehicle_id' => $this->vehicle_id,
            'cost_date' => $this->cost_date,
            'vendor_id' => $this->cost_type === 'service_parts' ? $this->vendor_id : null,
            'description' => $this->description,
            'total_price' => $totalPrice,
            'document' => $documentPath,
            'big_cash' => $this->big_cash ?? false,
            'created_by' => Auth::id(),
        ]);

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

    public function render()
    {
        $vehicles = Vehicle::with(['brand', 'vehicle_model'])
            ->where('status', '1')
            ->orderBy('police_number')
            ->get();

        $vendors = Vendor::orderBy('name')->get();

        return view('livewire.cost.cost-create', compact('vehicles', 'vendors'));
    }
}
