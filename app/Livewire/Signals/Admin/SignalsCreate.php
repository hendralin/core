<?php

namespace App\Livewire\Signals\Admin;

use Livewire\Component;
use App\Models\StockSignal;
use App\Models\TradingInfo;
use App\Models\StockCompany;
use App\Models\FinancialRatio;
use Illuminate\Support\Facades\Auth;

class SignalsCreate extends Component
{
    public $signal_type = 'manual';
    public $kode_emiten = '';
    public $status = 'draft';
    public $notes = '';
    public $recommendation = '';
    public $companyFound = false;

    protected function rules()
    {
        $baseRules = [
            'status' => 'required|in:draft,active,published,cancelled,expired',
            'notes' => 'nullable|string|max:1000',
            'recommendation' => 'required|string|max:2000',
        ];

        // For manual signals, only require basic information
        $baseRules = array_merge($baseRules, [
            'signal_type' => 'required|string',
            'kode_emiten' => 'required|string|max:10|exists:stock_companies,kode_emiten',
        ]);

        return $baseRules;
    }

    protected $validationAttributes = [
        'kode_emiten' => 'Kode Emiten',
        'status' => 'Status',
        'notes' => 'Catatan',
        'recommendation' => 'Rekomendasi',
    ];

    protected function messages()
    {
        return [
            'kode_emiten.exists' => 'Kode Emiten tidak ditemukan dalam database perusahaan saham.',
        ];
    }

    public function updatedKodeEmiten()
    {
        $this->kode_emiten = strtoupper($this->kode_emiten);

        // Check if company exists
        if ($this->kode_emiten) {
            $company = StockCompany::where('kode_emiten', $this->kode_emiten)->first();
            $this->companyFound = $company !== null;
        } else {
            $this->companyFound = false;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $createData = [
                'status' => $this->status,
                'notes' => $this->notes ?: null,
                'recommendation' => $this->recommendation,
                'user_id' => Auth::id(),
            ];

            // Set published_at based on status
            if ($this->status === 'published') {
                $createData['published_at'] = now();
            }

            // Get market cap for the company
            $marketCap = TradingInfo::where('kode_emiten', $this->kode_emiten)->latest('date')->first();
            if ($marketCap) {
                $createData['market_cap'] = $marketCap->close * $marketCap->listed_shares;
            }

            // Get financial ratios for the company
            $financialRatios = FinancialRatio::where('code', $this->kode_emiten)->latest('fs_date')->first();
            if ($financialRatios) {
                $createData['pbv'] = $financialRatios->price_bv;
                $createData['per'] = $financialRatios->per;
            }

            // Add signal data (all fields are now optional for manual signals)
            $createData = array_merge($createData, [
                'signal_type' => $this->signal_type,
                'kode_emiten' => strtoupper($this->kode_emiten),
            ]);

            StockSignal::create($createData);

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
