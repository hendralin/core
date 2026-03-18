<?php

namespace App\Livewire\CashInject;

use Carbon\Carbon;
use App\Models\Cost;
use App\Models\Warehouse;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Title('Edit Inject Kas')]
class CashInjectEdit extends Component
{
    use WithFileUploads;

    public Cost $cost;

    public $cost_type;
    public $cost_date;
    public $warehouse_id;
    public $description;
    public $total_price;
    public $document;
    public $existing_document;

    public function mount(Cost $cost)
    {
        $this->cost = $cost;

        // Check if this is actually a cash inject (Kas Kecil or Kas Pajak)
        if (!in_array($cost->cost_type, ['cash', 'tax_cash'])) {
            abort(403, 'Record ini bukan merupakan inject kas.' . auth()->id());
        }

        // Check if user has permission to edit inject kas
        if ($cost->cost_type === 'cash' && auth()->id() == 8) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit inject kas kecil.');
        } elseif ($cost->cost_type === 'tax_cash' && auth()->id() == 2) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit inject kas pajak.');
        }

        $this->cost_type = $cost->cost_type;
        $this->cost_date = Carbon::parse($cost->cost_date)->format('Y-m-d');
        $this->warehouse_id = $cost->warehouse_id;
        $this->description = $cost->description;
        $this->total_price = number_format($cost->total_price, 0);
        $this->existing_document = $cost->document;
    }

    public function submit()
    {
        $rules = [
            'cost_type' => 'required|in:cash,tax_cash',
            'cost_date' => 'required|date|before_or_equal:today',
            'warehouse_id' => 'required|exists:warehouses,id',
            'description' => 'required|string',
            'total_price' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ];

        $messages = [
            'cost_type.required' => 'Tipe kas harus dipilih.',
            'cost_type.in' => 'Tipe kas harus berupa Kas Kecil atau Kas Pajak.',
            'cost_date.required' => 'Tanggal inject kas harus dipilih.',
            'cost_date.date' => 'Tanggal inject kas harus berupa tanggal.',
            'cost_date.before_or_equal' => 'Tanggal inject kas tidak boleh lebih dari hari ini.',
            'warehouse_id.required' => 'Warehouse harus dipilih.',
            'warehouse_id.exists' => 'Warehouse yang dipilih tidak valid.',
            'description.required' => 'Deskripsi inject harus diisi.',
            'description.string' => 'Deskripsi inject harus berupa teks.',
            'total_price.required' => 'Total inject harus diisi.',
            'total_price.string' => 'Total inject harus berupa angka.',
            'document.file' => 'Dokumen harus berupa file.',
            'document.mimes' => 'Dokumen harus berupa PDF, JPG, JPEG, atau PNG.',
            'document.max' => 'Dokumen maksimal ukuran 5MB.',
        ];

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
            'warehouse_id' => $this->cost->getOriginal('warehouse_id'),
            'cost_date' => $this->cost->getOriginal('cost_date'),
            'description' => $this->cost->getOriginal('description'),
            'total_price' => $this->cost->getOriginal('total_price'),
            'document' => $this->cost->getOriginal('document'),
        ];

        $this->cost->update([
            'cost_type' => $this->cost_type,
            'cost_date' => $this->cost_date,
            'warehouse_id' => $this->warehouse_id,
            'description' => $this->description,
            'total_price' => $totalPrice,
            'document' => $documentPath,
        ]);

        // Log the update activity with changes
        activity()
            ->performedOn($this->cost)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'cost_type' => $this->cost_type,
                    'cost_date' => $this->cost_date,
                    'warehouse_id' => $this->warehouse_id,
                    'description' => $this->description,
                    'total_price' => $totalPrice,
                    'document' => $documentPath,
                ]
            ])
            ->log('updated cash inject record');

        if ($this->cost_type === 'tax_cash') {
            session()->flash('success', 'Inject kas pajak berhasil diperbarui.');
        } else {
            session()->flash('success', 'Inject kas kecil berhasil diperbarui.');
        }

        return $this->redirect('/cash-injects', true);
    }

    public function removeDocument()
    {
        // Delete existing document
        if ($this->existing_document) {
            Storage::disk('public')->delete('photos/costs/' . $this->existing_document);
            $this->existing_document = null;
        }
        $this->cost->update(['document' => null]);
    }

    public function render()
    {
        $warehouses = Warehouse::where('has_cash', true)->orderBy('name')->get();

        return view('livewire.cash-inject.cash-inject-edit', compact('warehouses'));
    }
}
