<?php

namespace App\Livewire\Signals\Admin;

use App\Models\StockSignal;
use App\Models\StockCompany;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SignalsCreate extends Component
{
    public $signal_type = 'manual';
    public $kode_emiten = '';
    public $market_cap = '';
    public $pbv = '';
    public $per = '';
    public $hit_date = '';
    public $hit_value = '';
    public $hit_close = '';
    public $hit_volume = '';
    public $before_date = '';
    public $before_value = '';
    public $before_close = '';
    public $before_volume = '';
    public $after_date = '';
    public $after_value = '';
    public $after_close = '';
    public $after_volume = '';
    public $status = 'draft';
    public $notes = '';
    public $recommendation = '';

    protected $rules = [
        'signal_type' => 'required|string',
        'kode_emiten' => 'required|string|max:10',
        'market_cap' => 'nullable|numeric|min:0',
        'pbv' => 'nullable|numeric|min:0',
        'per' => 'nullable|numeric|min:0',
        'hit_date' => 'required|date',
        'hit_value' => 'required|numeric|min:0',
        'hit_close' => 'required|numeric|min:0',
        'hit_volume' => 'required|integer|min:0',
        'before_date' => 'nullable|date',
        'before_value' => 'nullable|numeric|min:0',
        'before_close' => 'nullable|numeric|min:0',
        'before_volume' => 'nullable|integer|min:0',
        'after_date' => 'nullable|date',
        'after_value' => 'nullable|numeric|min:0',
        'after_close' => 'nullable|numeric|min:0',
        'after_volume' => 'nullable|integer|min:0',
        'status' => 'required|in:draft,active,published',
        'notes' => 'nullable|string|max:1000',
        'recommendation' => 'required|string|max:2000',
    ];

    protected $validationAttributes = [
        'kode_emiten' => 'Kode Emiten',
        'market_cap' => 'Market Cap',
        'pbv' => 'PBV',
        'per' => 'PER',
        'hit_date' => 'Tanggal Hit',
        'hit_value' => 'Nilai Hit',
        'hit_close' => 'Harga Close Hit',
        'hit_volume' => 'Volume Hit',
        'before_date' => 'Tanggal Sebelum',
        'before_value' => 'Nilai Sebelum',
        'before_close' => 'Harga Close Sebelum',
        'before_volume' => 'Volume Sebelum',
        'after_date' => 'Tanggal Sesudah',
        'after_value' => 'Nilai Sesudah',
        'after_close' => 'Harga Close Sesudah',
        'after_volume' => 'Volume Sesudah',
        'status' => 'Status',
        'notes' => 'Catatan',
        'recommendation' => 'Rekomendasi',
    ];

    public function updatedKodeEmiten()
    {
        $this->kode_emiten = strtoupper($this->kode_emiten);

        // Try to get company info if it exists
        if ($this->kode_emiten) {
            $company = StockCompany::where('kode_emiten', $this->kode_emiten)->first();
            if ($company) {
                $this->market_cap = $company->market_cap ?? null;
                // You could also fetch PBV and PER from financial ratios if needed
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            StockSignal::create([
                'signal_type' => $this->signal_type,
                'kode_emiten' => strtoupper($this->kode_emiten),
                'market_cap' => $this->market_cap ?: null,
                'pbv' => $this->pbv ?: null,
                'per' => $this->per ?: null,
                'hit_date' => $this->hit_date,
                'hit_value' => $this->hit_value,
                'hit_close' => $this->hit_close,
                'hit_volume' => $this->hit_volume,
                'before_date' => $this->before_date ?: null,
                'before_value' => $this->before_value ?: null,
                'before_close' => $this->before_close ?: null,
                'before_volume' => $this->before_volume ?: null,
                'after_date' => $this->after_date ?: null,
                'after_value' => $this->after_value ?: null,
                'after_close' => $this->after_close ?: null,
                'after_volume' => $this->after_volume ?: null,
                'status' => $this->status,
                'published_at' => $this->status === 'published' ? now() : null,
                'notes' => $this->notes ?: null,
                'recommendation' => $this->recommendation,
                'user_id' => Auth::id(),
            ]);

            session()->flash('success', 'Sinyal berhasil dibuat.');

            return $this->redirect('/admin/signals', true);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.signals.admin.signals-create');
    }
}
