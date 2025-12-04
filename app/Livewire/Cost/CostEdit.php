<?php

namespace App\Livewire\Cost;

use Carbon\Carbon;
use App\Models\Cost;
use App\Models\Vendor;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Title('Edit Pembukuan Modal')]
class CostEdit extends Component
{
    use WithFileUploads;

    public Cost $cost;

    public $cost_type;
    public $vehicle_id;
    public $cost_date;
    public $vendor_id;
    public $description;
    public $total_price;
    public $document;
    public $existing_document;

    public function mount(Cost $cost)
    {
        $this->cost = $cost;

        // Only allow editing if status is pending
        if ($cost->status !== 'pending') {
            abort(403, 'Tidak dapat mengubah pembukuan modal yang telah disetujui atau ditolak.');
        }

        $this->cost_type = $cost->cost_type;
        $this->vehicle_id = $cost->vehicle_id;
        $this->cost_date = Carbon::parse($cost->cost_date)->format('Y-m-d');
        $this->vendor_id = $cost->vendor_id;
        $this->description = $cost->description;
        $this->total_price = number_format($cost->total_price, 0);
        $this->existing_document = $cost->document;
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

        $documentPath = $this->existing_document;
        if ($this->document) {
            // Delete old document if exists
            if ($this->existing_document) {
                Storage::disk('public')->delete('photos/costs/' . $this->existing_document);
            }
            $storedPath = $this->document->store('photos/costs', 'public');
            $documentPath = basename($storedPath);
            $this->existing_document = $documentPath;
            $this->document = null;
        }

        // Parse formatted number back to numeric
        $totalPrice = Str::replace(',', '', $this->total_price);

        // Store old values before update (since update() clears dirty attributes)
        $oldValues = [
            'cost_type' => $this->cost->getOriginal('cost_type'),
            'vehicle_id' => $this->cost->getOriginal('vehicle_id'),
            'cost_date' => $this->cost->getOriginal('cost_date'),
            'vendor_id' => $this->cost->getOriginal('vendor_id'),
            'description' => $this->cost->getOriginal('description'),
            'total_price' => $this->cost->getOriginal('total_price'),
            'document' => $this->cost->getOriginal('document'),
        ];

        $this->cost->update([
            'cost_type' => $this->cost_type,
            'vehicle_id' => $this->vehicle_id,
            'cost_date' => $this->cost_date,
            'vendor_id' => $this->cost_type === 'service_parts' ? $this->vendor_id : null,
            'description' => $this->description,
            'total_price' => $totalPrice,
            'document' => $documentPath,
        ]);

        // Log the update activity
        activity()
            ->performedOn($this->cost)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'cost_type' => $this->cost_type,
                    'vehicle_id' => $this->vehicle_id,
                    'cost_date' => $this->cost_date,
                    'vendor_id' => $this->cost_type === 'service_parts' ? $this->vendor_id : null,
                    'description' => $this->description,
                    'total_price' => $totalPrice,
                    'document' => $documentPath,
                ]
            ])
            ->log('updated cost record');

        session()->flash('success', 'Pembukuan modal berhasil diubah.');

        return $this->redirect('/costs', true);
    }

    public function removeDocument()
    {
        if ($this->existing_document) {
            Storage::disk('public')->delete('photos/costs/' . $this->existing_document);
            $this->cost->update(['document' => null]);
            $this->existing_document = null;
            $this->document = null;

            session()->flash('success', 'Dokumen berhasil dihapus.');
        }
    }


    public function render()
    {
        $vehicles = Vehicle::with(['brand', 'vehicle_model'])
            ->where('status', '1')
            ->orderBy('police_number')
            ->get();

        $vendors = Vendor::orderBy('name')->get();

        return view('livewire.cost.cost-edit', compact('vehicles', 'vendors'));
    }
}
