<?php

namespace App\Livewire\CashDisbursement;

use Carbon\Carbon;
use App\Models\Cost;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Title('Edit Pengeluaran Kas')]
class CashDisbursementEdit extends Component
{
    use WithFileUploads;

    public Cost $cost;

    public $cost_type;
    public $cost_date;
    public $description;
    public $total_price;
    public $document;
    public $existing_document;

    public function mount(Cost $cost)
    {
        $this->cost = $cost;

        // Only allow editing if status is pending and cost_type is cash or other_cost
        if ($cost->status !== 'pending') {
            abort(403, 'Tidak dapat mengubah pengeluaran kas yang telah disetujui atau ditolak.');
        }

        if (!in_array($cost->cost_type, ['cash', 'other_cost'])) {
            abort(403, 'Record ini bukan merupakan pengeluaran kas.');
        }

        $this->cost_type = $cost->cost_type;
        $this->cost_date = Carbon::parse($cost->cost_date)->format('Y-m-d');
        $this->description = $cost->description;
        $this->total_price = number_format($cost->total_price, 0);
        $this->existing_document = $cost->document;
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
            'cost_date.required' => 'Tanggal pengeluaran kas harus dipilih.',
            'cost_date.date' => 'Tanggal pengeluaran kas harus berupa tanggal.',
            'cost_date.before_or_equal' => 'Tanggal pengeluaran kas tidak boleh lebih dari hari ini.',
            'description.required' => 'Deskripsi pengeluaran harus diisi.',
            'description.string' => 'Deskripsi pengeluaran harus berupa teks.',
            'total_price.required' => 'Total pengeluaran harus diisi.',
            'total_price.string' => 'Total pengeluaran harus berupa angka.',
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
            'cost_date' => $this->cost->getOriginal('cost_date'),
            'description' => $this->cost->getOriginal('description'),
            'total_price' => $this->cost->getOriginal('total_price'),
            'document' => $this->cost->getOriginal('document'),
        ];

        $this->cost->update([
            'cost_type' => $this->cost_type,
            'cost_date' => $this->cost_date,
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
                    'description' => $this->description,
                    'total_price' => $totalPrice,
                    'document' => $documentPath,
                ]
            ])
            ->log('updated cash disbursement record');

        session()->flash('success', 'Pengeluaran kas berhasil diperbarui.');

        return $this->redirect('/cash-disbursements', true);
    }

    public function removeDocument()
    {
        // Delete existing document
        if ($this->existing_document) {
            Storage::disk('public')->delete('photos/costs/' . $this->existing_document);
            $this->existing_document = null;
        }
        $this->document = null;
    }

    public function render()
    {
        return view('livewire.cash-disbursement.cash-disbursement-edit');
    }
}
