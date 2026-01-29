<?php

namespace App\Livewire\CashDisbursement;

use App\Models\Cost;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Tambah Biaya Showroom')]
class CashDisbursementCreate extends Component
{
    use WithFileUploads;

    public $cost_type = 'showroom';
    public $cost_date;
    public $description;
    public $total_price;
    public $document;

    public function mount()
    {
        // Set default cost date to today
        $this->cost_date = now()->format('Y-m-d');
    }

    public function submit()
    {
        $rules = [
            'cost_date' => 'required|date|before_or_equal:today',
            'description' => 'required|string',
            'total_price' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ];

        $messages = [
            'cost_date.required' => 'Tanggal biaya showroom harus dipilih.',
            'cost_date.date' => 'Tanggal biaya showroom harus berupa tanggal.',
            'cost_date.before_or_equal' => 'Tanggal biaya showroom tidak boleh lebih dari hari ini.',
            'description.required' => 'Deskripsi biaya showroom harus diisi.',
            'description.string' => 'Deskripsi biaya showroom harus berupa teks.',
            'total_price.required' => 'Total biaya showroom harus diisi.',
            'total_price.string' => 'Total biaya showroom harus berupa angka.',
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

        // Parse formatted number back to numeric
        $totalPrice = Str::replace(',', '', $this->total_price);

        $cost = Cost::create([
            'cost_type' => $this->cost_type,
            'vehicle_id' => null, // No vehicle for biaya showroom
            'cost_date' => $this->cost_date,
            'vendor_id' => null, // No vendor for biaya showroom
            'description' => $this->description,
            'total_price' => $totalPrice,
            'document' => $documentPath,
            'created_by' => Auth::id(),
        ]);

        // Log the creation activity
        activity()
            ->performedOn($cost)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'cost_type' => $this->cost_type,
                    'cost_date' => $this->cost_date,
                    'description' => $this->description,
                    'total_price' => $totalPrice,
                    'document' => $documentPath,
                ]
            ])
            ->log('created biaya showroom record');

        session()->flash('success', 'Biaya showroom berhasil ditambahkan.');

        return $this->redirect('/cash-disbursements', true);
    }

    public function removeDocument()
    {
        $this->document = null;
    }

    public function render()
    {
        return view('livewire.cash-disbursement.cash-disbursement-create');
    }
}
