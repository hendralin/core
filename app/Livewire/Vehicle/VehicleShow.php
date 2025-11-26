<?php

namespace App\Livewire\Vehicle;

use App\Models\Cost;
use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title('Show Vehicle')]
class VehicleShow extends Component
{
    use WithPagination, WithoutUrlPagination;

    public Vehicle $vehicle;
    public $recentActivities;
    public $costSummary;

    // Modal properties
    public $showBuyerModal = false;

    // Buyer information
    public $buyer_name = '';
    public $buyer_phone = '';
    public $buyer_address = '';

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle->load(['brand', 'type', 'category', 'vehicle_model', 'warehouse', 'images']);

        // Get cost summary for this vehicle
        $this->loadCostSummary();

        // Get recent activities for this vehicle
        $this->recentActivities = Activity::query()
            ->with(['causer'])
            ->where('subject_type', Vehicle::class)
            ->where('subject_id', $vehicle->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Load existing buyer data if available
        $this->buyer_name = $vehicle->buyer_name ?? '';
        $this->buyer_phone = $vehicle->buyer_phone ?? '';
        $this->buyer_address = $vehicle->buyer_address ?? '';
    }

    public function loadCostSummary()
    {
        $allCosts = Cost::query()
            ->where('vehicle_id', $this->vehicle->id)
            ->get();

        $this->costSummary = [
            'total' => $allCosts->sum('total_price'),
            'service_parts' => $allCosts->where('cost_type', 'service_parts')->sum('total_price'),
            'other_cost' => $allCosts->where('cost_type', 'other_cost')->sum('total_price'),
        ];
    }

    public function getPriceAnalysis()
    {
        $purchasePrice = $this->vehicle->purchase_price ?? 0;
        $displayPrice = $this->vehicle->display_price ?? 0;
        $sellingPrice = $this->vehicle->selling_price ?? 0;

        // Total modal = harga beli + total cost (semua status)
        $totalModal = $purchasePrice + $this->costSummary['total'];

        // Total modal approved = harga beli + cost approved saja
        $approvedCosts = Cost::query()
            ->where('vehicle_id', $this->vehicle->id)
            ->where('status', 'approved')
            ->sum('total_price');
        $totalModalApproved = $purchasePrice + $approvedCosts;

        $recommendedMinPrice = max($totalModal, $totalModalApproved);

        $analysis = [
            'purchase_price' => $purchasePrice,
            'display_price' => $displayPrice,
            'selling_price' => $sellingPrice,
            'has_selling_price' => $sellingPrice > 0,
            'total_cost_all' => $this->costSummary['total'],
            'total_cost_approved' => $approvedCosts,
            'total_modal_all' => $totalModal,
            'total_modal_approved' => $totalModalApproved,
            'recommended_min_price' => $recommendedMinPrice,
            'is_display_price_correct' => $displayPrice >= $recommendedMinPrice,
            'is_selling_price_correct' => $sellingPrice > 0 ? $sellingPrice >= $recommendedMinPrice : null,
            'display_price_difference' => $displayPrice - $recommendedMinPrice,
            'selling_price_difference' => $sellingPrice > 0 ? $sellingPrice - $recommendedMinPrice : 0,
            'display_profit_margin' => $displayPrice > 0 ? (($displayPrice - $recommendedMinPrice) / $recommendedMinPrice) * 100 : 0,
            'selling_profit_margin' => $sellingPrice > 0 ? (($sellingPrice - $recommendedMinPrice) / $recommendedMinPrice) * 100 : 0,
            'price_vs_selling_gap' => $sellingPrice > 0 ? $displayPrice - $sellingPrice : 0,
        ];

        return $analysis;
    }

    public function render()
    {
        return view('livewire.vehicle.vehicle-show', [
            'costs' => $this->getCosts(),
            'priceAnalysis' => $this->getPriceAnalysis()
        ]);
    }

    public function getCosts()
    {
        return Cost::query()
            ->with(['vendor'])
            ->where('vehicle_id', $this->vehicle->id)
            ->orderBy('cost_date', 'desc')
            ->paginate(10);
    }

    public function openBuyerModal()
    {
        $this->showBuyerModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function updatedShowBuyerModal($value)
    {
        if ($value) {
            // Modal dibuka, reset validasi
            $this->resetValidation();
            $this->resetErrorBag();
        }
    }

    public function printReceipt()
    {
        // Validate buyer information
        $this->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_phone' => 'required|string|max:20',
            'buyer_address' => 'required|string|max:1000',
        ], [
            'buyer_name.required' => 'Nama pembeli harus diisi.',
            'buyer_phone.required' => 'Nomor telepon pembeli harus diisi.',
            'buyer_address.required' => 'Alamat pembeli harus diisi.',
        ]);

        // Generate receipt number if not exists
        $receiptNumber = $this->generateReceiptNumber();

        // Update vehicle with buyer information and receipt number
        $this->vehicle->update([
            'buyer_name' => $this->buyer_name,
            'buyer_phone' => $this->buyer_phone,
            'buyer_address' => $this->buyer_address,
            'receipt_number' => $receiptNumber,
        ]);

        // Close modal
        $this->showBuyerModal = false;

        // Load additional relationships needed for the receipt
        $vehicle = $this->vehicle->fresh(['salesman']);

        $pdf = Pdf::loadView('exports.vehicle-receipt', [
            'vehicle' => $vehicle
        ]);

        $filename = 'kwitansi_' . $vehicle->police_number . '_' . now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    private function generateReceiptNumber()
    {
        // Check if receipt number already exists
        if ($this->vehicle->receipt_number) {
            return $this->vehicle->receipt_number;
        }

        // Format: KW/YYYYMMDD/XXXXX
        $prefix = 'KW';
        $year = now()->format('Y');
        $date = now()->format('Ymd');

        // Get the last receipt number for this year
        $lastReceipt = Vehicle::where('receipt_number', 'like', $prefix . '/' . $year . '%')
            ->orderBy('receipt_number', 'desc')
            ->first();

        if ($lastReceipt) {
            // Extract the sequence number and increment
            $parts = explode('/', $lastReceipt->receipt_number);
            $sequence = (int) end($parts);
            $sequence++;
        } else {
            // Start from 00001 for new year
            $sequence = 1;
        }

        // Format sequence with leading zeros (5 digits)
        $formattedSequence = str_pad($sequence, 5, '0', STR_PAD_LEFT);

        return $prefix . '/' . $date . '/' . $formattedSequence;
    }

    private function numberToWords($number)
    {
        $words = [
            0 => 'nol',
            1 => 'satu',
            2 => 'dua',
            3 => 'tiga',
            4 => 'empat',
            5 => 'lima',
            6 => 'enam',
            7 => 'tujuh',
            8 => 'delapan',
            9 => 'sembilan',
            10 => 'sepuluh',
            11 => 'sebelas',
            12 => 'dua belas',
            13 => 'tiga belas',
            14 => 'empat belas',
            15 => 'lima belas',
            16 => 'enam belas',
            17 => 'tujuh belas',
            18 => 'delapan belas',
            19 => 'sembilan belas',
            20 => 'dua puluh',
            30 => 'tiga puluh',
            40 => 'empat puluh',
            50 => 'lima puluh',
            60 => 'enam puluh',
            70 => 'tujuh puluh',
            80 => 'delapan puluh',
            90 => 'sembilan puluh'
        ];

        $result = '';

        if ($number == 0) {
            return '';
        }

        // Handle numbers less than 20
        if ($number < 20) {
            $result = $words[$number];
        }
        // Handle numbers less than 100
        elseif ($number < 100) {
            $tens = floor($number / 10) * 10;
            $units = $number % 10;
            $result = $words[$tens];
            if ($units > 0) {
                $result .= ' ' . $words[$units];
            }
        }
        // Handle numbers less than 1000 (hundreds)
        elseif ($number < 1000) {
            $hundreds = floor($number / 100);
            $remainder = $number % 100;

            if ($hundreds == 1) {
                $result = 'seratus';
            } else {
                $result = $words[$hundreds] . ' ratus';
            }

            if ($remainder > 0) {
                $result .= ' ' . $this->numberToWords($remainder);
            }
        }
        // Handle numbers less than 1 million (thousands)
        elseif ($number < 1000000) {
            $thousands = floor($number / 1000);
            $remainder = $number % 1000;

            if ($thousands == 1) {
                $result = 'seribu';
            } elseif ($thousands < 100) {
                $result = $this->numberToWords($thousands) . ' ribu';
            } else {
                $result = $this->numberToWords($thousands) . ' ribu';
            }

            if ($remainder > 0) {
                $result .= ' ' . $this->numberToWords($remainder);
            }
        }
        // Handle numbers less than 1 billion (millions)
        elseif ($number < 1000000000) {
            $millions = floor($number / 1000000);
            $remainder = $number % 1000000;

            $result = $this->numberToWords($millions) . ' juta';

            if ($remainder > 0) {
                $result .= ' ' . $this->numberToWords($remainder);
            }
        }
        // Handle larger numbers
        elseif ($number < 1000000000000) {
            $billions = floor($number / 1000000000);
            $remainder = $number % 1000000000;

            $result = $this->numberToWords($billions) . ' miliar';

            if ($remainder > 0) {
                $result .= ' ' . $this->numberToWords($remainder);
            }
        } else {
            return 'Angka terlalu besar';
        }

        return trim($result);
    }

    public function getAmountInWords($amount)
    {
        if ($amount <= 0) {
            return 'nol rupiah';
        }

        // Round to nearest rupiah (no decimals for rupiah)
        $rupiah = floor($amount);

        // Convert to words
        $words = $this->numberToWords($rupiah);

        // Handle special cases for Indonesian currency
        // Change "satu ribu" to "seribu", "satu ratus" to "seratus", etc.
        $words = preg_replace('/\bsatu ribu\b/', 'seribu', $words);
        $words = preg_replace('/\bsatu ratus\b/', 'seratus', $words);

        // Capitalize first letter
        $result = ucfirst($words) . ' rupiah';

        return $result;
    }
}
