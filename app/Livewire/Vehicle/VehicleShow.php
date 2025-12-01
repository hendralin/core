<?php

namespace App\Livewire\Vehicle;

use App\Models\Cost;
use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Activity;
use App\Models\Commission;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\LoanCalculation;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Show Vehicle')]
class VehicleShow extends Component
{
    use WithPagination, WithoutUrlPagination;

    public Vehicle $vehicle;
    public $recentActivities;
    public $costSummary;

    // Modal properties
    public $showBuyerModal = false;
    public $showCommissionModal = false;

    // Buyer information
    public $buyer_name = '';
    public $buyer_phone = '';
    public $buyer_address = '';

    // Commission form properties
    public $commission_date = '';
    public $commission_description = '';
    public $commission_amount = '';
    public $commission_type = 2; // Default to purchase commission

    // Edit commission properties
    public $editingCommissionId = null;
    public $showEditCommissionModal = false;

    // Loan calculation form properties
    public $showLoanCalculationModal = false;
    public $loan_calculation_leasing_id = '';
    public $loan_calculation_description = '';

    // Edit loan calculation properties
    public $editingLoanCalculationId = null;
    public $showEditLoanCalculationModal = false;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle->load(['brand', 'type', 'category', 'vehicle_model', 'warehouse', 'images', 'commissions', 'equipment', 'loanCalculations']);

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

    public function openCommissionModal()
    {
        $this->resetCommissionForm();
        $this->showCommissionModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeCommissionModal()
    {
        $this->showCommissionModal = false;
        $this->resetCommissionForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function createCommission()
    {
        $this->validate([
            'commission_date' => 'required|date',
            'commission_description' => 'required|string|max:255',
            'commission_amount' => 'required|string',
            'commission_type' => 'required|in:1,2',
        ], [
            'commission_date.required' => 'Tanggal komisi harus diisi.',
            'commission_date.date' => 'Format tanggal tidak valid.',
            'commission_description.required' => 'Deskripsi komisi harus diisi.',
            'commission_description.max' => 'Deskripsi maksimal 255 karakter.',
            'commission_amount.required' => 'Jumlah komisi harus diisi.',
            'commission_amount.string' => 'Jumlah komisi harus berupa angka.',
            'commission_type.required' => 'Tipe komisi harus dipilih.',
            'commission_type.in' => 'Tipe komisi tidak valid.',
        ]);

        $commissionAmount = Str::replace(',', '', $this->commission_amount);

        $commission = Commission::create([
            'commission_date' => $this->commission_date,
            'type' => $this->commission_type,
            'vehicle_id' => $this->vehicle->id,
            'amount' => $commissionAmount,
            'description' => $this->commission_description,
        ]);

        // Reload vehicle with commissions
        $this->vehicle->load('commissions');

        // Log the creation activity with detailed information
        activity()
            ->performedOn($commission)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'commission_date' => $this->commission_date,
                    'type' => $this->commission_type,
                    'amount' => $commissionAmount,
                    'description' => $this->commission_description,
                ]
            ])
            ->log('created commission');

        // Show success message
        session()->flash('message', 'Komisi ' . ($this->commission_type == 1 ? 'Penjualan' : 'Pembelian') . ' berhasil ditambahkan.');

        // Close modal
        $this->closeCommissionModal();
    }

    private function resetCommissionForm()
    {
        $this->reset('commission_date', 'commission_description', 'commission_amount', 'commission_type');
    }

    public function openEditCommissionModal($commissionId)
    {
        $commission = Commission::findOrFail($commissionId);

        // Check if commission belongs to this vehicle
        if ($commission->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $this->editingCommissionId = $commissionId;
        $this->commission_date = $commission->commission_date;
        $this->commission_description = $commission->description;
        $this->commission_amount = number_format($commission->amount, 0);
        $this->commission_type = $commission->type;

        $this->showEditCommissionModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeEditCommissionModal()
    {
        $this->showEditCommissionModal = false;
        $this->editingCommissionId = null;
        $this->resetCommissionForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function updateCommission()
    {
        $this->validate([
            'commission_date' => 'required|date',
            'commission_description' => 'required|string|max:255',
            'commission_amount' => 'required|string',
            'commission_type' => 'required|in:1,2',
        ], [
            'commission_date.required' => 'Tanggal komisi harus diisi.',
            'commission_date.date' => 'Format tanggal tidak valid.',
            'commission_description.required' => 'Deskripsi komisi harus diisi.',
            'commission_description.max' => 'Deskripsi maksimal 255 karakter.',
            'commission_amount.required' => 'Jumlah komisi harus diisi.',
            'commission_amount.string' => 'Jumlah komisi harus berupa angka.',
            'commission_type.required' => 'Tipe komisi harus dipilih.',
            'commission_type.in' => 'Tipe komisi tidak valid.',
        ]);

        $commission = Commission::findOrFail($this->editingCommissionId);

        // Check if commission belongs to this vehicle
        if ($commission->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $commissionAmount = Str::replace(',', '', $this->commission_amount);

        // Store old commission data for logging
        $oldCommission = [
            'commission_date' => $commission->commission_date,
            'type' => $commission->type,
            'amount' => $commission->amount,
            'description' => $commission->description,
        ];

        // Update commission data
        $commission->update([
            'commission_date' => $this->commission_date,
            'type' => $this->commission_type,
            'amount' => $commissionAmount,
            'description' => $this->commission_description,
        ]);

        // Reload vehicle with commissions
        $this->vehicle->load('commissions');

        // Log the update activity with detailed information
        activity()
            ->performedOn($commission)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldCommission,
                'attributes' => [
                    'commission_date' => $this->commission_date,
                    'type' => $this->commission_type,
                    'amount' => $commissionAmount,
                    'description' => $this->commission_description,
                ]
            ])
            ->log('updated commission');

        // Show success message
        session()->flash('message', 'Komisi ' . ($this->commission_type == 1 ? 'Penjualan' : 'Pembelian') . ' berhasil diperbarui.');

        // Close modal
        $this->closeEditCommissionModal();
    }

    public function deleteCommission($commissionId)
    {
        $commission = Commission::findOrFail($commissionId);

        // Check if commission belongs to this vehicle
        if ($commission->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $commission->delete();

        // Reload vehicle with commissions
        $this->vehicle->load('commissions');

        // Log the deletion activity
        activity()
            ->performedOn($commission)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => [
                    'vehicle_id' => $this->vehicle->id,
                    'commission_date' => $commission->commission_date,
                    'type' => $commission->type,
                    'amount' => $commission->amount,
                    'description' => $commission->description,
                ],
            ])
            ->log('deleted commission');

        // Show success message
        session()->flash('message', 'Komisi ' . ($commission->type == 1 ? 'Penjualan' : 'Pembelian') . ' berhasil dihapus.');
    }

    // Loan Calculation Methods
    public function openLoanCalculationModal()
    {
        $this->resetLoanCalculationForm();
        $this->showLoanCalculationModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeLoanCalculationModal()
    {
        $this->showLoanCalculationModal = false;
        $this->resetLoanCalculationForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function createLoanCalculation()
    {
        $this->validate([
            'loan_calculation_leasing_id' => 'required|exists:leasings,id',
            'loan_calculation_description' => 'required|string|max:255',
        ], [
            'loan_calculation_leasing_id.required' => 'Leasing harus dipilih.',
            'loan_calculation_leasing_id.exists' => 'Leasing tidak valid.',
            'loan_calculation_description.required' => 'Deskripsi harus diisi.',
            'loan_calculation_description.max' => 'Deskripsi maksimal 255 karakter.',
        ]);

        $loanCalculation = LoanCalculation::create([
            'vehicle_id' => $this->vehicle->id,
            'leasing_id' => $this->loan_calculation_leasing_id,
            'description' => $this->loan_calculation_description,
        ]);

        // Reload vehicle with loan calculations
        $this->vehicle->load('loanCalculations');

        // Log the creation activity
        activity()
            ->performedOn($loanCalculation)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'vehicle_id' => $this->vehicle->id,
                    'leasing_id' => $this->loan_calculation_leasing_id,
                    'description' => $this->loan_calculation_description,
                ]
            ])
            ->log('created loan calculation');

        // Show success message
        session()->flash('message', 'Perhitungan kredit berhasil ditambahkan.');

        // Close modal
        $this->closeLoanCalculationModal();
    }

    private function resetLoanCalculationForm()
    {
        $this->reset('loan_calculation_leasing_id', 'loan_calculation_description');
    }

    public function openEditLoanCalculationModal($loanCalculationId)
    {
        $loanCalculation = LoanCalculation::findOrFail($loanCalculationId);

        // Check if loan calculation belongs to this vehicle
        if ($loanCalculation->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $this->editingLoanCalculationId = $loanCalculationId;
        $this->loan_calculation_leasing_id = $loanCalculation->leasing_id;
        $this->loan_calculation_description = $loanCalculation->description;

        $this->showEditLoanCalculationModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeEditLoanCalculationModal()
    {
        $this->showEditLoanCalculationModal = false;
        $this->resetLoanCalculationForm();
        $this->editingLoanCalculationId = null;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function updateLoanCalculation()
    {
        $this->validate([
            'loan_calculation_leasing_id' => 'required|exists:leasings,id',
            'loan_calculation_description' => 'required|string|max:255',
        ], [
            'loan_calculation_leasing_id.required' => 'Leasing harus dipilih.',
            'loan_calculation_leasing_id.exists' => 'Leasing tidak valid.',
            'loan_calculation_description.required' => 'Deskripsi harus diisi.',
            'loan_calculation_description.max' => 'Deskripsi maksimal 255 karakter.',
        ]);

        $loanCalculation = LoanCalculation::findOrFail($this->editingLoanCalculationId);

        // Check if loan calculation belongs to this vehicle
        if ($loanCalculation->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $oldLoanCalculation = [
            'vehicle_id' => $this->vehicle->id,
            'leasing_id' => $loanCalculation->leasing_id,
            'description' => $loanCalculation->description,
        ];

        $loanCalculation->update([
            'leasing_id' => $this->loan_calculation_leasing_id,
            'description' => $this->loan_calculation_description,
        ]);

        // Reload vehicle with loan calculations
        $this->vehicle->load('loanCalculations');

        // Log the update activity
        activity()
            ->performedOn($loanCalculation)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldLoanCalculation,
                'attributes' => [
                    'vehicle_id' => $this->vehicle->id,
                    'leasing_id' => $this->loan_calculation_leasing_id,
                    'description' => $this->loan_calculation_description,
                ],
            ])
            ->log('updated loan calculation');

        // Show success message
        session()->flash('message', 'Perhitungan kredit berhasil diperbarui.');

        // Close modal
        $this->closeEditLoanCalculationModal();
    }

    public function deleteLoanCalculation($loanCalculationId)
    {
        $loanCalculation = LoanCalculation::findOrFail($loanCalculationId);

        // Check if loan calculation belongs to this vehicle
        if ($loanCalculation->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $loanCalculation->delete();

        // Reload vehicle with loan calculations
        $this->vehicle->load('loanCalculations');

        // Log the deletion activity
        activity()
            ->performedOn($loanCalculation)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => [
                    'vehicle_id' => $this->vehicle->id,
                    'leasing_id' => $loanCalculation->leasing_id,
                    'description' => $loanCalculation->description,
                ],
            ])
            ->log('deleted loan calculation');

        // Show success message
        session()->flash('message', 'Perhitungan kredit berhasil dihapus.');
    }
}
