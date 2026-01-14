<?php

namespace App\Livewire\Vehicle;

use App\Models\Cost;
use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Activity;
use App\Models\Commission;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\PaymentReceipt;
use App\Models\VehicleCertificateReceipt;
use App\Models\VehicleFile;
use App\Models\VehicleFileTitle;
use Livewire\Attributes\Title;
use App\Models\LoanCalculation;
use App\Models\PurchasePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\VehicleHandover;

#[Title('Show Vehicle')]
class VehicleShow extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    public Vehicle $vehicle;
    public $recentActivities;
    public $costSummary;
    public $vehicleFileTitles;

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

    // Purchase payment form properties
    public $showPurchasePaymentModal = false;
    public $purchase_payment_date = '';
    public $purchase_payment_description = '';
    public $purchase_payment_amount = '';
    public $purchase_payment_document = [];

    // Edit purchase payment properties
    public $showEditPurchasePaymentModal = false;
    public $editingPurchasePaymentId = null;

    // Payment receipt form properties
    public $showPaymentReceiptModal = false;
    public $payment_receipt_date = '';
    public $payment_receipt_description = '';
    public $payment_receipt_amount = '';
    public $payment_receipt_must_be_settled_date = '';
    public $payment_receipt_document = [];

    // Edit payment receipt properties
    public $showEditPaymentReceiptModal = false;
    public $editingPaymentReceiptId = null;

    // Certificate receipt modal properties
    public $showCertificateReceiptModal = false;

    // Edit certificate receipt properties
    public $showEditCertificateReceiptModal = false;
    public $editingCertificateReceiptId = null;

    // Certificate receipt form properties
    public $certificate_receipt_number = '';
    public $in_the_name_of = '';
    public $original_invoice_name = '';
    public $photocopy_id_card_name = '';
    public $receipt_form = '';
    public $nik = '';
    public $form_a = '';
    public $release_of_title_letter = '';
    public $others = '';
    public $receipt_date = '';
    public $transferee = '';
    public $receiving_party = '';

    // Handover modal properties
    public $showHandoverModal = false;

    // Edit handover properties
    public $showEditHandoverModal = false;
    public $editingHandoverId = null;

    // Handover form properties
    public $handover_number = '';
    public $handover_date = '';
    public $handover_from = '';
    public $handover_to = '';
    public $handover_from_address = '';
    public $handover_to_address = '';
    public $handover_transferee = '';
    public $handover_receiving_party = '';

    // Handover file upload properties
    public $showUploadHandoverModal = false;
    public $uploadingHandoverId = null;
    public $handover_file = [];

    // Certificate receipt file upload properties
    public $showUploadCertificateReceiptModal = false;
    public $uploadingCertificateReceiptId = null;
    public $certificate_receipt_file = [];

    // Vehicle file modal properties
    public $showFileModal = false;
    public $vehicle_file_title_id = '';
    public $vehicle_file = [];

    // Edit vehicle file properties
    public $editingVehicleFileId = null;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle->load(['brand', 'type', 'category', 'vehicle_model', 'warehouse', 'images', 'commissions', 'equipment', 'loanCalculations', 'purchasePayments', 'paymentReceipts', 'vehicleCertificateReceipts', 'vehicleHandovers', 'vehicleFiles']);

        // Load vehicle file titles
        $this->vehicleFileTitles = VehicleFileTitle::all();

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

        // Hitung komisi pembelian (type = 2)
        $purchaseCommission = $this->vehicle->commissions->where('type', 2)->sum('amount');

        // Hitung komisi penjualan (type = 1)
        $sellingCommission = $this->vehicle->commissions->where('type', 1)->sum('amount');

        // Total modal = harga beli + total cost (semua status) + komisi pembelian
        $totalModal = $purchasePrice + $this->costSummary['total'] + $purchaseCommission;

        // Total modal approved = harga beli + cost approved saja + komisi pembelian
        $approvedCosts = Cost::query()
            ->where('vehicle_id', $this->vehicle->id)
            ->where('status', 'approved')
            ->sum('total_price');
        $totalModalApproved = $purchasePrice + $approvedCosts + $purchaseCommission;

        $recommendedMinPrice = max($totalModal, $totalModalApproved);

        $analysis = [
            'purchase_price' => $purchasePrice,
            'display_price' => $displayPrice,
            'selling_price' => $sellingPrice,
            'has_selling_price' => $sellingPrice > 0,
            'total_cost_all' => $this->costSummary['total'],
            'total_cost_approved' => $approvedCosts,
            'purchase_commission' => $purchaseCommission,
            'selling_commission' => $sellingCommission,
            'total_modal_all' => $totalModal,
            'total_modal_approved' => $totalModalApproved,
            'recommended_min_price' => $recommendedMinPrice,
            'is_display_price_correct' => $displayPrice >= $recommendedMinPrice,
            'is_selling_price_correct' => $sellingPrice > 0 ? $sellingPrice >= $recommendedMinPrice : null,
            'display_price_difference' => $displayPrice - $recommendedMinPrice,
            'selling_price_difference' => $sellingPrice > 0 ? $sellingPrice - $sellingCommission - $recommendedMinPrice : 0,
            'display_profit_margin' => $displayPrice > 0 ? (($displayPrice - $recommendedMinPrice) / $displayPrice) * 100 : 0,
            'selling_profit_margin' => $sellingPrice > 0 ? (($sellingPrice - $sellingCommission - $recommendedMinPrice) / $sellingPrice) * 100 : 0,
            'price_vs_selling_gap' => $sellingPrice > 0 ? $displayPrice - $sellingPrice : 0,
        ];

        return $analysis;
    }

    public function render()
    {
        return view('livewire.vehicle.vehicle-show', [
            'costs' => $this->getCosts(),
            'priceAnalysis' => $this->getPriceAnalysis(),
            'vehicleFileTitles' => $this->vehicleFileTitles,
            'editingVehicleFileId' => $this->editingVehicleFileId
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
        // Get the last payment receipt for this vehicle
        $paymentReceipt = $this->vehicle->paymentReceipts()->latest()->first();

        if (!$paymentReceipt) {
            session()->flash('error', 'Tidak ada penerimaan pembayaran untuk kendaraan ini.');
            return;
        }

        // Update print count
        if ($paymentReceipt->print_count == 0) {
            $paymentReceipt->update(['printed_at' => now()]);
        }
        $paymentReceipt->increment('print_count');

        // Log the print activity
        activity()
            ->performedOn($paymentReceipt)
            ->causedBy(auth()->user())
            ->withProperties(['print_count' => $paymentReceipt->print_count])
            ->log('printed payment receipt');

        // Load payment receipt with vehicle relationship
        $paymentReceipt->load(['vehicle']);

        $pdf = Pdf::loadView('exports.kwitansi', [
            'paymentReceipt' => $paymentReceipt
        ]);

        $filename = 'Kwitansi_' . str_replace(['/', '\\'], '_', $paymentReceipt->payment_number) . '.pdf';

        // Close modal
        $this->showBuyerModal = false;

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
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

    // Certificate Receipt Methods
    public function createCertificateReceipt()
    {
        // Check if certificate receipt already exists for this vehicle
        if ($this->vehicle->vehicleCertificateReceipts && $this->vehicle->vehicleCertificateReceipts->count() > 0) {
            session()->flash('error', 'Tanda Terima BPKB sudah dibuat untuk kendaraan ini.');
            $this->closeCertificateReceiptModal();
            return;
        }

        $this->validate([
            'in_the_name_of' => 'required|string|max:255',
            'original_invoice_name' => 'required|string|max:255',
            'photocopy_id_card_name' => 'required|string|max:255',
            'receipt_form' => 'required|string|max:255',
            'nik' => 'required|string|max:16',
            'form_a' => 'required|string|max:255',
            'release_of_title_letter' => 'required|string|max:255',
            'others' => 'nullable|string|max:255',
            'receipt_date' => 'required|date',
            'transferee' => 'required|string|max:255',
            'receiving_party' => 'required|string|max:255',
        ], [
            'in_the_name_of.required' => 'BPKB A/N harus diisi.',
            'original_invoice_name.required' => 'Faktur Asli A/N harus diisi.',
            'photocopy_id_card_name.required' => 'Fotocopy KTP A/N harus diisi.',
            'receipt_form.required' => 'Blanko Kwitansi harus diisi.',
            'nik.required' => 'NIK harus diisi.',
            'nik.max' => 'NIK maksimal 16 karakter.',
            'form_a.required' => 'Form A harus diisi.',
            'release_of_title_letter.required' => 'Surat Pelepasan Hak harus diisi.',
            'others.max' => 'Lain-lain maksimal 255 karakter.',
            'receipt_date.required' => 'Tanggal tanda terima harus diisi.',
            'receipt_date.date' => 'Format tanggal tidak valid.',
            'transferee.required' => 'Yang menyerahkan harus diisi.',
            'receiving_party.required' => 'Yang menerima harus diisi.',
        ]);

        // Generate certificate receipt number
        $certificateReceiptNumber = $this->generateCertificateReceiptNumber();

        $certificateReceipt = VehicleCertificateReceipt::create([
            'vehicle_id' => $this->vehicle->id,
            'certificate_receipt_number' => $certificateReceiptNumber,
            'in_the_name_of' => $this->in_the_name_of,
            'original_invoice_name' => $this->original_invoice_name,
            'photocopy_id_card_name' => $this->photocopy_id_card_name,
            'receipt_form' => $this->receipt_form,
            'nik' => $this->nik,
            'form_a' => $this->form_a,
            'release_of_title_letter' => $this->release_of_title_letter,
            'others' => $this->others,
            'receipt_date' => $this->receipt_date,
            'transferee' => $this->transferee,
            'receiving_party' => $this->receiving_party,
            'created_by' => Auth::id(),
        ]);

        // Reload vehicle with certificate receipts
        $this->vehicle->load('vehicleCertificateReceipts');

        // Log the creation activity
        activity()
            ->performedOn($certificateReceipt)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'certificate_receipt_number' => $certificateReceiptNumber,
                    'in_the_name_of' => $this->in_the_name_of,
                    'original_invoice_name' => $this->original_invoice_name,
                    'photocopy_id_card_name' => $this->photocopy_id_card_name,
                    'receipt_form' => $this->receipt_form,
                    'nik' => $this->nik,
                    'form_a' => $this->form_a,
                    'release_of_title_letter' => $this->release_of_title_letter,
                    'others' => $this->others,
                    'receipt_date' => $this->receipt_date,
                    'transferee' => $this->transferee,
                    'receiving_party' => $this->receiving_party,
                ]
            ])
            ->log('created vehicle certificate receipt');

        // Show success message
        session()->flash('message', 'Tanda Terima BPKB berhasil dibuat.');

        // Close modal
        $this->closeCertificateReceiptModal();
    }

    public function openCertificateReceiptModal()
    {
        $this->resetCertificateReceiptForm();
        $this->showCertificateReceiptModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeCertificateReceiptModal()
    {
        $this->showCertificateReceiptModal = false;
        $this->resetCertificateReceiptForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    private function resetCertificateReceiptForm()
    {
        $this->reset([
            'certificate_receipt_number',
            'in_the_name_of',
            'original_invoice_name',
            'photocopy_id_card_name',
            'receipt_form',
            'nik',
            'form_a',
            'release_of_title_letter',
            'others',
            'receipt_date',
            'transferee',
            'receiving_party'
        ]);
    }

    private function generateCertificateReceiptNumber()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Convert month to Roman numerals
        $romanMonths = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V',
            '06' => 'VI', '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X',
            '11' => 'XI', '12' => 'XII'
        ];

        $romanMonth = $romanMonths[$currentMonth];

        // Get the next sequential number for this year
        $lastReceipt = VehicleCertificateReceipt::whereYear('created_at', $currentYear)
            ->orderBy('certificate_receipt_number', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastReceipt) {
            // Extract the sequential number from the last receipt number
            $parts = explode('/', $lastReceipt->certificate_receipt_number);
            if (count($parts) >= 1) {
                $lastNumber = (int) $parts[0];
                $nextNumber = $lastNumber + 1;
            }
        }

        // Format: 001/TT/BPKB/WOTO/XII/2025
        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '/TT/BPKB/WOTO/' . $romanMonth . '/' . $currentYear;
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

    // Purchase Payment Modal Methods
    public function openPurchasePaymentModal()
    {
        $this->resetPurchasePaymentForm();
        $this->showPurchasePaymentModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closePurchasePaymentModal()
    {
        $this->showPurchasePaymentModal = false;
        $this->resetPurchasePaymentForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function resetPurchasePaymentForm()
    {
        $this->purchase_payment_date = '';
        $this->purchase_payment_description = '';
        $this->purchase_payment_amount = '';
        $this->purchase_payment_document = [];
    }

    public function generatePaymentNumber()
    {
        $now = now();
        $month = $now->month;
        $year = $now->year;
        $monthRoman = $this->monthToRoman($month);

        // Get the last payment number for this month and year
        $lastPayment = PurchasePayment::where('payment_number', 'like', "%/PP/WOTO/{$monthRoman}/{$year}")
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPayment) {
            // Extract the sequential number and increment
            $parts = explode('/', $lastPayment->payment_number);
            $sequence = (int) $parts[0];
            $sequence++;
        } else {
            // Start from 0001 for new month/year
            $sequence = 1;
        }

        // Format sequence with leading zeros (4 digits)
        $formattedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return "{$formattedSequence}/PP/WOTO/{$monthRoman}/{$year}";
    }

    private function monthToRoman($month)
    {
        $romanNumerals = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romanNumerals[$month] ?? 'I';
    }

    private function getTotalPurchasePayments()
    {
        return $this->vehicle->purchasePayments->sum('amount');
    }

    public function savePurchasePayment()
    {
        // Check if user has permission to create purchase payment
        $this->authorize('vehicle-purchase-payment.create', $this->vehicle);

        // Check if total payments would exceed purchase price
        $currentTotalPaid = (float) $this->getTotalPurchasePayments();
        $newPaymentAmount = (float) Str::replace(',', '', $this->purchase_payment_amount);
        $purchasePrice = (float) ($this->vehicle->purchase_price ?? 0);

        if (($currentTotalPaid + $newPaymentAmount) > $purchasePrice) {
            session()->flash('error', 'Total pembayaran tidak boleh melebihi harga beli kendaraan.');
            return;
        }

        // Validate the form
        $this->validate([
            'purchase_payment_date' => 'required|date',
            'purchase_payment_description' => 'required|string|max:255',
            'purchase_payment_amount' => 'required|string',
            'purchase_payment_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'purchase_payment_date.required' => 'Tanggal pembayaran harus diisi.',
            'purchase_payment_date.date' => 'Tanggal pembayaran tidak valid.',
            'purchase_payment_description.required' => 'Deskripsi pembayaran harus diisi.',
            'purchase_payment_description.string' => 'Deskripsi pembayaran harus berupa teks.',
            'purchase_payment_description.max' => 'Deskripsi pembayaran maksimal 255 karakter.',
            'purchase_payment_amount.required' => 'Jumlah pembayaran harus diisi.',
            'purchase_payment_amount.string' => 'Jumlah pembayaran harus berupa angka.',
            'purchase_payment_document.file' => 'Dokumen harus berupa file.',
            'purchase_payment_document.mimes' => 'Dokumen harus berupa file PDF, JPG, JPEG, atau PNG.',
            'purchase_payment_document.max' => 'Ukuran dokumen maksimal 2MB.',
        ]);

        // Handle file upload if provided
        $documentPath = null;
        if ($this->purchase_payment_document) {
            $fileNames = [];

            // Always treat as array since we use multiple attribute
            $files = (array) $this->purchase_payment_document;

            foreach ($files as $file) {
                if ($file && is_object($file)) { // Make sure it's a valid file object
                    $storedPath = $file->store('documents/purchase-payments', 'public');
                    $fileNames[] = basename($storedPath);
                }
            }

            if (!empty($fileNames)) {
                $documentPath = implode(',', $fileNames);
            }
        }

        // Generate payment number
        $paymentNumber = $this->generatePaymentNumber();

        // Create purchase payment
        $purchasePayment = PurchasePayment::create([
            'vehicle_id' => $this->vehicle->id,
            'payment_number' => $paymentNumber,
            'payment_date' => $this->purchase_payment_date,
            'amount' => Str::replace(',', '', $this->purchase_payment_amount),
            'description' => $this->purchase_payment_description,
            'document' => $documentPath,
            'created_by' => Auth::id(),
            'status' => 'approved', // approved status
        ]);

        // Reload vehicle with purchase payments
        $this->vehicle->load('purchasePayments');

        // Log the creation activity
        activity()
            ->performedOn($purchasePayment)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'vehicle_id' => $this->vehicle->id,
                    'payment_number' => $paymentNumber,
                    'payment_date' => $this->purchase_payment_date,
                    'amount' => Str::replace(',', '', $this->purchase_payment_amount),
                    'description' => $this->purchase_payment_description,
                    'document' => $documentPath,
                ],
            ])
            ->log('created purchase payment');

        // Show success message
        session()->flash('message', 'Pembayaran pembelian berhasil ditambahkan.');

        // Close modal
        $this->closePurchasePaymentModal();
    }

    public function editPurchasePayment($purchasePaymentId)
    {
        $purchasePayment = PurchasePayment::findOrFail($purchasePaymentId);

        // Check if purchase payment belongs to this vehicle
        if ($purchasePayment->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        // Fill form with existing data
        $this->editingPurchasePaymentId = $purchasePaymentId;
        $this->purchase_payment_date = $purchasePayment->payment_date ? \Carbon\Carbon::parse($purchasePayment->payment_date)->format('Y-m-d') : '';
        $this->purchase_payment_description = $purchasePayment->description;
        $this->purchase_payment_amount = number_format($purchasePayment->amount, 0);
        // Keep existing document, don't reset it

        $this->showEditPurchasePaymentModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeEditPurchasePaymentModal()
    {
        $this->showEditPurchasePaymentModal = false;
        $this->editingPurchasePaymentId = null;
        $this->resetPurchasePaymentForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function updatePurchasePayment()
    {
        // Check if user has permission to edit purchase payment
        $this->authorize('vehicle-purchase-payment.edit', $this->vehicle);

        $purchasePayment = PurchasePayment::findOrFail($this->editingPurchasePaymentId);

        // Check if total payments would exceed purchase price after update
        $currentTotalPaid = (float) $this->getTotalPurchasePayments() - (float) $purchasePayment->amount; // Subtract current amount
        $newPaymentAmount = (float) Str::replace(',', '', $this->purchase_payment_amount);
        $purchasePrice = (float) ($this->vehicle->purchase_price ?? 0);

        if (($currentTotalPaid + $newPaymentAmount) > $purchasePrice) {
            session()->flash('error', 'Total pembayaran tidak boleh melebihi harga beli kendaraan.');
            return;
        }

        // Validate the form
        $this->validate([
            'purchase_payment_date' => 'required|date',
            'purchase_payment_description' => 'required|string|max:255',
            'purchase_payment_amount' => 'required|string',
            'purchase_payment_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'purchase_payment_date.required' => 'Tanggal pembayaran harus diisi.',
            'purchase_payment_date.date' => 'Tanggal pembayaran tidak valid.',
            'purchase_payment_description.required' => 'Deskripsi pembayaran harus diisi.',
            'purchase_payment_description.string' => 'Deskripsi pembayaran harus berupa teks.',
            'purchase_payment_description.max' => 'Deskripsi pembayaran maksimal 255 karakter.',
            'purchase_payment_amount.required' => 'Jumlah pembayaran harus diisi.',
            'purchase_payment_amount.string' => 'Jumlah pembayaran harus berupa angka.',
            'purchase_payment_document.file' => 'Dokumen harus berupa file.',
            'purchase_payment_document.mimes' => 'Dokumen harus berupa file PDF, JPG, JPEG, atau PNG.',
            'purchase_payment_document.max' => 'Ukuran dokumen maksimal 2MB.',
        ]);

        $purchasePayment = PurchasePayment::findOrFail($this->editingPurchasePaymentId);

        // Check if purchase payment belongs to this vehicle
        if ($purchasePayment->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        // Store old data for logging
        $oldPurchasePayment = $purchasePayment->toArray();

        // Handle file upload if provided
        $documentPath = $purchasePayment->document; // Keep existing document
        if ($this->purchase_payment_document) {
            // Delete old files if exist
            if ($purchasePayment->document) {
                $oldFiles = explode(',', $purchasePayment->document);
                foreach ($oldFiles as $oldFile) {
                    Storage::disk('public')->delete('documents/purchase-payments/' . trim($oldFile));
                }
            }

            $fileNames = [];

            // Always treat as array since we use multiple attribute
            $files = (array) $this->purchase_payment_document;

            foreach ($files as $file) {
                if ($file && is_object($file)) { // Make sure it's a valid file object
                    $storedPath = $file->store('documents/purchase-payments', 'public');
                    $fileNames[] = basename($storedPath);
                }
            }

            if (!empty($fileNames)) {
                $documentPath = implode(',', $fileNames);
            }
        }

        // Update purchase payment
        $purchasePayment->update([
            'payment_date' => $this->purchase_payment_date,
            'amount' => Str::replace(',', '', $this->purchase_payment_amount),
            'description' => $this->purchase_payment_description,
            'document' => $documentPath,
        ]);

        // Reload vehicle with purchase payments
        $this->vehicle->load('purchasePayments');

        // Log the update activity
        activity()
            ->performedOn($purchasePayment)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldPurchasePayment,
                'attributes' => [
                    'payment_date' => $this->purchase_payment_date,
                    'amount' => Str::replace(',', '', $this->purchase_payment_amount),
                    'description' => $this->purchase_payment_description,
                    'document' => $documentPath,
                ],
            ])
            ->log('updated purchase payment');

        // Show success message
        session()->flash('message', 'Pembayaran pembelian berhasil diperbarui.');

        // Close modal
        $this->closeEditPurchasePaymentModal();
    }

    public function deletePurchasePayment($purchasePaymentId)
    {
        // Check if user has permission to delete purchase payment
        $this->authorize('vehicle-purchase-payment.delete', $this->vehicle);

        $purchasePayment = PurchasePayment::findOrFail($purchasePaymentId);

        // Check if purchase payment belongs to this vehicle
        if ($purchasePayment->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        // Store old data for logging
        $oldPurchasePayment = $purchasePayment->toArray();

        // Delete files if exist
        if ($purchasePayment->document) {
            $files = explode(',', $purchasePayment->document);
            foreach ($files as $file) {
                Storage::disk('public')->delete('documents/purchase-payments/' . trim($file));
            }
        }

        $purchasePayment->delete();

        // Reload vehicle with purchase payments
        $this->vehicle->load('purchasePayments');

        // Log the deletion activity
        activity()
            ->performedOn($purchasePayment)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldPurchasePayment,
            ])
            ->log('deleted purchase payment');

        // Show success message
        session()->flash('message', 'Pembayaran pembelian berhasil dihapus.');
    }

    // Payment Receipt Methods
    public function openPaymentReceiptModal()
    {
        $this->resetPaymentReceiptForm();
        $this->showPaymentReceiptModal = true;
    }

    public function closePaymentReceiptModal()
    {
        $this->showPaymentReceiptModal = false;
        $this->resetPaymentReceiptForm();
    }

    public function resetPaymentReceiptForm()
    {
        $this->payment_receipt_date = '';
        $this->payment_receipt_description = '';
        $this->payment_receipt_amount = '';
        $this->payment_receipt_must_be_settled_date = '';
        $this->payment_receipt_document = [];
    }

    private function generatePaymentReceiptNumber()
    {
        $monthRoman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][date('n') - 1];
        $year = date('Y');

        // Get the last payment receipt number for this month
        $lastPaymentReceipt = PaymentReceipt::where('payment_number', 'like', "%/PR/WOTO/{$monthRoman}/{$year}")
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPaymentReceipt) {
            $parts = explode('/', $lastPaymentReceipt->payment_number);
            $lastNumber = (int) $parts[0];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return str_pad($newNumber, 3, '0', STR_PAD_LEFT) . "/PR/WOTO/{$monthRoman}/{$year}";
    }

    private function parseFormatted($value)
    {
        // Remove thousand separators (periods) and replace comma with dot for decimal
        $cleaned = str_replace('.', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }

    private function getTotalPaymentReceipts()
    {
        return $this->vehicle->paymentReceipts->sum('amount');
    }

    public function savePaymentReceipt()
    {
        $this->validate([
            'payment_receipt_date' => 'required|date',
            'payment_receipt_amount' => 'required|string',
            'payment_receipt_description' => 'nullable|string|max:255',
            'payment_receipt_must_be_settled_date' => 'nullable|date|after:today',
            'payment_receipt_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Additional validation: check total payments against selling price
        $totalPaymentsIncludingCurrent = (float) $this->getTotalPaymentReceipts() + (float) Str::replace(',', '', $this->payment_receipt_amount);

        // Total pembayaran tidak boleh melebihi harga jual
        if ($totalPaymentsIncludingCurrent > (float) $this->vehicle->selling_price) {
            $this->addError('payment_receipt_amount', 'Total pembayaran tidak boleh melebihi Harga Jual Kendaraan (Rp ' . number_format($this->vehicle->selling_price, 0, ',', '.') . ').');
            return;
        }

        // must_be_settled_date is required if total payments < selling_price
        if ($totalPaymentsIncludingCurrent < $this->vehicle->selling_price && empty($this->payment_receipt_must_be_settled_date)) {
            $this->addError('payment_receipt_must_be_settled_date', 'Tanggal harus diselesaikan wajib diisi jika total pembayaran masih kurang dari harga jual.');
            return;
        }

        $paymentNumber = $this->generatePaymentReceiptNumber();

        $documentPaths = [];
        if ($this->payment_receipt_document) {
            foreach ($this->payment_receipt_document as $document) {
                $originalName = $document->getClientOriginalName();
                $fileName = time() . '_' . $originalName;
                $document->storeAs('documents/payment-receipts', $fileName, 'public');
                $documentPaths[] = $fileName;
            }
        }

        $paymentReceipt = PaymentReceipt::create([
            'vehicle_id' => $this->vehicle->id,
            'payment_number' => $paymentNumber,
            'payment_date' => $this->payment_receipt_date,
            'amount' => Str::replace(',', '', $this->payment_receipt_amount),
            'description' => $this->payment_receipt_description,
            'remaining_balance' => (float) $this->vehicle->selling_price - ((float) $this->getTotalPaymentReceipts() + (float) Str::replace(',', '', $this->payment_receipt_amount)),
            'must_be_settled_date' => $this->payment_receipt_must_be_settled_date ?: null,
            'document' => $documentPaths ? implode(',', $documentPaths) : null,
            'created_by' => Auth::id(),
            'status' => 'approved',
        ]);

        $this->closePaymentReceiptModal();

        // Reload vehicle with payment receipts
        $this->vehicle->load('paymentReceipts');

        // Log the creation activity
        activity()
            ->performedOn($paymentReceipt)
            ->causedBy(Auth::user())
            ->log('created payment receipt');

        // Show success message
        session()->flash('message', 'Penerimaan pembayaran berhasil ditambahkan.');
    }

    public function editPaymentReceipt($paymentReceiptId)
    {
        $paymentReceipt = PaymentReceipt::findOrFail($paymentReceiptId);

        $this->editingPaymentReceiptId = $paymentReceiptId;
        $this->payment_receipt_date = $paymentReceipt->payment_date ? date('Y-m-d', strtotime($paymentReceipt->payment_date)) : '';
        $this->payment_receipt_description = $paymentReceipt->description;
        $this->payment_receipt_amount = number_format($paymentReceipt->amount, 0);
        $this->payment_receipt_must_be_settled_date = $paymentReceipt->must_be_settled_date ? date('Y-m-d', strtotime($paymentReceipt->must_be_settled_date)) : '';
        $this->payment_receipt_document = [];

        $this->showEditPaymentReceiptModal = true;
    }

    public function closeEditPaymentReceiptModal()
    {
        $this->showEditPaymentReceiptModal = false;
        $this->editingPaymentReceiptId = null;
        $this->resetPaymentReceiptForm();
    }

    public function updatePaymentReceipt()
    {
        $this->validate([
            'payment_receipt_date' => 'required|date',
            'payment_receipt_amount' => 'required|string',
            'payment_receipt_description' => 'nullable|string|max:255',
            'payment_receipt_must_be_settled_date' => 'nullable|date|after:today',
            'payment_receipt_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Additional validation: check total payments against selling price
        $paymentReceipt = PaymentReceipt::findOrFail($this->editingPaymentReceiptId);
        $totalPaymentsExcludingCurrent = (float) $this->getTotalPaymentReceipts() - (float) $paymentReceipt->amount;
        $totalPaymentsIncludingUpdated = $totalPaymentsExcludingCurrent + (float) Str::replace(',', '', $this->payment_receipt_amount);

        // Total pembayaran tidak boleh melebihi harga jual
        if ($totalPaymentsIncludingUpdated > (float) $this->vehicle->selling_price) {
            $this->addError('payment_receipt_amount', 'Total pembayaran tidak boleh melebihi Harga Jual Kendaraan (Rp ' . number_format($this->vehicle->selling_price, 0, ',', '.') . ').');
            return;
        }

        // must_be_settled_date is required if total payments < selling_price
        if ($totalPaymentsIncludingUpdated < $this->vehicle->selling_price && empty($this->payment_receipt_must_be_settled_date)) {
            $this->addError('payment_receipt_must_be_settled_date', 'Tanggal harus diselesaikan wajib diisi jika total pembayaran masih kurang dari harga jual.');
            return;
        }

        $oldPaymentReceipt = $paymentReceipt->toArray();

        $documentPaths = $paymentReceipt->document ? explode(',', $paymentReceipt->document) : [];

        // Handle new documents
        if ($this->payment_receipt_document) {
            foreach ($this->payment_receipt_document as $document) {
                $originalName = $document->getClientOriginalName();
                $fileName = time() . '_' . $originalName;
                $document->storeAs('documents/payment-receipts', $fileName, 'public');
                $documentPaths[] = $fileName;
            }
        }

        $paymentReceipt->update([
            'payment_date' => $this->payment_receipt_date,
            'amount' => Str::replace(',', '', $this->payment_receipt_amount),
            'description' => $this->payment_receipt_description,
            'remaining_balance' => (float) $this->vehicle->selling_price - (((float) $this->getTotalPaymentReceipts() - (float) $paymentReceipt->amount) + (float) Str::replace(',', '', $this->payment_receipt_amount)),
            'must_be_settled_date' => $this->payment_receipt_must_be_settled_date ?: null,
            'document' => $documentPaths ? implode(',', $documentPaths) : null,
        ]);

        $this->closeEditPaymentReceiptModal();

        // Reload vehicle with payment receipts
        $this->vehicle->load('paymentReceipts');

        // Log the update activity
        activity()
            ->performedOn($paymentReceipt)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldPaymentReceipt,
                'attributes' => $paymentReceipt->toArray(),
            ])
            ->log('updated payment receipt');

        // Show success message
        session()->flash('message', 'Penerimaan pembayaran berhasil diperbarui.');
    }

    public function deletePaymentReceipt($paymentReceiptId)
    {
        $paymentReceipt = PaymentReceipt::findOrFail($paymentReceiptId);

        $oldPaymentReceipt = $paymentReceipt->toArray();

        // Delete files if exist
        if ($paymentReceipt->document) {
            $files = explode(',', $paymentReceipt->document);
            foreach ($files as $file) {
                Storage::disk('public')->delete('documents/payment-receipts/' . trim($file));
            }
        }

        $paymentReceipt->delete();

        // Reload vehicle with payment receipts
        $this->vehicle->load('paymentReceipts');

        // Log the deletion activity
        activity()
            ->performedOn($paymentReceipt)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldPaymentReceipt,
            ])
            ->log('deleted payment receipt');

        // Show success message
        session()->flash('message', 'Penerimaan pembayaran berhasil dihapus.');
    }

    public function deleteCertificateReceipt($certificateReceiptId)
    {
        $certificateReceipt = VehicleCertificateReceipt::findOrFail($certificateReceiptId);

        // Check if certificate receipt belongs to this vehicle
        if ($certificateReceipt->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $oldCertificateReceipt = $certificateReceipt->toArray();

        // Delete file if exists
        if ($certificateReceipt->receipt_file) {
            Storage::disk('public')->delete('documents/registration-certificate-receipts/' . $certificateReceipt->receipt_file);
        }

        $certificateReceipt->delete();

        // Reload vehicle with certificate receipts
        $this->vehicle->load('vehicleCertificateReceipts');

        // Log the deletion activity
        activity()
            ->performedOn($certificateReceipt)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldCertificateReceipt,
            ])
            ->log('deleted vehicle certificate receipt');

        // Show success message
        session()->flash('message', 'Tanda Terima BPKB berhasil dihapus.');
    }

    public function editRegistrationCertificateReceipt($certificateReceiptId)
    {
        $certificateReceipt = VehicleCertificateReceipt::findOrFail($certificateReceiptId);

        // Check if certificate receipt belongs to this vehicle
        if ($certificateReceipt->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $this->editingCertificateReceiptId = $certificateReceiptId;
        $this->in_the_name_of = $certificateReceipt->in_the_name_of;
        $this->original_invoice_name = $certificateReceipt->original_invoice_name;
        $this->photocopy_id_card_name = $certificateReceipt->photocopy_id_card_name;
        $this->receipt_form = $certificateReceipt->receipt_form;
        $this->nik = $certificateReceipt->nik;
        $this->form_a = $certificateReceipt->form_a;
        $this->release_of_title_letter = $certificateReceipt->release_of_title_letter;
        $this->others = $certificateReceipt->others;
        $this->receipt_date = $certificateReceipt->receipt_date ? date('Y-m-d', strtotime($certificateReceipt->receipt_date)) : '';
        $this->transferee = $certificateReceipt->transferee;
        $this->receiving_party = $certificateReceipt->receiving_party;

        $this->showEditCertificateReceiptModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeEditCertificateReceiptModal()
    {
        $this->showEditCertificateReceiptModal = false;
        $this->editingCertificateReceiptId = null;
        $this->resetCertificateReceiptForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function updateCertificateReceipt()
    {
        $this->validate([
            'in_the_name_of' => 'required|string|max:255',
            'original_invoice_name' => 'required|string|max:255',
            'photocopy_id_card_name' => 'required|string|max:255',
            'receipt_form' => 'required|string|max:255',
            'nik' => 'required|string|max:16',
            'form_a' => 'required|string|max:255',
            'release_of_title_letter' => 'required|string|max:255',
            'others' => 'nullable|string|max:255',
            'receipt_date' => 'required|date',
            'transferee' => 'required|string|max:255',
            'receiving_party' => 'required|string|max:255',
        ], [
            'in_the_name_of.required' => 'BPKB A/N harus diisi.',
            'original_invoice_name.required' => 'Faktur Asli A/N harus diisi.',
            'photocopy_id_card_name.required' => 'Fotocopy KTP A/N harus diisi.',
            'receipt_form.required' => 'Blanko Kwitansi harus diisi.',
            'nik.required' => 'NIK harus diisi.',
            'nik.max' => 'NIK maksimal 16 karakter.',
            'form_a.required' => 'Form A harus diisi.',
            'release_of_title_letter.required' => 'Surat Pelepasan Hak harus diisi.',
            'others.max' => 'Lain-lain maksimal 255 karakter.',
            'receipt_date.required' => 'Tanggal tanda terima harus diisi.',
            'receipt_date.date' => 'Format tanggal tidak valid.',
            'transferee.required' => 'Yang menyerahkan harus diisi.',
            'receiving_party.required' => 'Yang menerima harus diisi.',
        ]);

        $certificateReceipt = VehicleCertificateReceipt::findOrFail($this->editingCertificateReceiptId);

        $oldCertificateReceipt = $certificateReceipt->toArray();

        $certificateReceipt->update([
            'in_the_name_of' => $this->in_the_name_of,
            'original_invoice_name' => $this->original_invoice_name,
            'photocopy_id_card_name' => $this->photocopy_id_card_name,
            'receipt_form' => $this->receipt_form,
            'nik' => $this->nik,
            'form_a' => $this->form_a,
            'release_of_title_letter' => $this->release_of_title_letter,
            'others' => $this->others,
            'receipt_date' => $this->receipt_date,
            'transferee' => $this->transferee,
            'receiving_party' => $this->receiving_party,
        ]);

        // Reload vehicle with certificate receipts
        $this->vehicle->load('vehicleCertificateReceipts');

        // Log the update activity
        activity()
            ->performedOn($certificateReceipt)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldCertificateReceipt,
                'attributes' => [
                    'certificate_receipt_number' => $this->certificate_receipt_number,
                    'in_the_name_of' => $this->in_the_name_of,
                    'original_invoice_name' => $this->original_invoice_name,
                    'photocopy_id_card_name' => $this->photocopy_id_card_name,
                    'receipt_form' => $this->receipt_form,
                    'nik' => $this->nik,
                    'form_a' => $this->form_a,
                    'release_of_title_letter' => $this->release_of_title_letter,
                    'others' => $this->others,
                    'receipt_date' => $this->receipt_date,
                    'transferee' => $this->transferee,
                    'receiving_party' => $this->receiving_party,
                ]
            ])
            ->log('updated vehicle certificate receipt');

        // Show success message
        session()->flash('message', 'Tanda Terima BPKB berhasil diperbarui.');

        // Close modal
        $this->closeEditCertificateReceiptModal();
    }

    // Handover Methods
    public function createHandover()
    {
        $this->validate([
            'handover_date' => 'required|date',
            'handover_from' => 'required|string|max:255',
            'handover_to' => 'required|string|max:255',
            'handover_from_address' => 'required|string|max:60',
            'handover_to_address' => 'required|string|max:60',
            'handover_transferee' => 'required|string|max:255',
            'handover_receiving_party' => 'required|string|max:255',
        ], [
            'handover_date.required' => 'Tanggal serah terima harus diisi.',
            'handover_date.date' => 'Format tanggal tidak valid.',
            'handover_from.required' => 'Serah terima dari harus diisi.',
            'handover_from.max' => 'Serah terima dari maksimal 255 karakter.',
            'handover_to.required' => 'Kepada harus diisi.',
            'handover_to.max' => 'Kepada maksimal 255 karakter.',
            'handover_from_address.required' => 'Alamat dari harus diisi.',
            'handover_from_address.max' => 'Alamat dari maksimal 60 karakter.',
            'handover_to_address.required' => 'Alamat kepada harus diisi.',
            'handover_to_address.max' => 'Alamat kepada maksimal 60 karakter.',
            'handover_transferee.required' => 'Yang menyerahkan harus diisi.',
            'handover_transferee.max' => 'Yang menyerahkan maksimal 255 karakter.',
            'handover_receiving_party.required' => 'Yang menerima harus diisi.',
            'handover_receiving_party.max' => 'Yang menerima maksimal 255 karakter.',
        ]);

        // Check if handover already exists for this vehicle
        if ($this->vehicle->vehicleHandovers && $this->vehicle->vehicleHandovers->count() > 0) {
            session()->flash('error', 'Berita Acara Serah Terima Kendaraan sudah dibuat untuk kendaraan ini.');
            $this->closeHandoverModal();
            return;
        }

        // Generate handover number
        $handoverNumber = $this->generateHandoverNumber();

        $handover = VehicleHandover::create([
            'vehicle_id' => $this->vehicle->id,
            'handover_number' => $handoverNumber,
            'handover_date' => $this->handover_date,
            'handover_from' => $this->handover_from,
            'handover_to' => $this->handover_to,
            'handover_from_address' => $this->handover_from_address,
            'handover_to_address' => $this->handover_to_address,
            'transferee' => $this->handover_transferee,
            'receiving_party' => $this->handover_receiving_party,
            'created_by' => Auth::id(),
        ]);

        // Reload vehicle with handovers
        $this->vehicle->load('vehicleHandovers');

        // Log the creation activity
        activity()
            ->performedOn($handover)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'handover_number' => $handoverNumber,
                    'handover_date' => $this->handover_date,
                    'handover_from' => $this->handover_from,
                    'handover_to' => $this->handover_to,
                    'handover_from_address' => $this->handover_from_address,
                    'handover_to_address' => $this->handover_to_address,
                    'transferee' => $this->handover_transferee,
                    'receiving_party' => $this->handover_receiving_party,
                ]
            ])
            ->log('created vehicle handover');

        // Show success message
        session()->flash('message', 'Berita Acara Serah Terima Kendaraan berhasil dibuat.');

        // Close modal
        $this->closeHandoverModal();
    }

    public function openHandoverModal()
    {
        $this->resetHandoverForm();
        $this->showHandoverModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeHandoverModal()
    {
        $this->showHandoverModal = false;
        $this->resetHandoverForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    private function resetHandoverForm()
    {
        $this->reset([
            'handover_number',
            'handover_date',
            'handover_from',
            'handover_to',
            'handover_from_address',
            'handover_to_address',
            'handover_transferee',
            'handover_receiving_party'
        ]);
    }

    private function generateHandoverNumber()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Convert month to Roman numerals
        $romanMonths = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V',
            '06' => 'VI', '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X',
            '11' => 'XI', '12' => 'XII'
        ];

        $romanMonth = $romanMonths[$currentMonth];

        // Get the next sequential number for this year
        $lastHandover = VehicleHandover::whereYear('created_at', $currentYear)
            ->orderBy('handover_number', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastHandover) {
            // Extract the sequential number from the last handover number
            $parts = explode('/', $lastHandover->handover_number);
            if (count($parts) >= 1) {
                $lastNumber = (int) $parts[0];
                $nextNumber = $lastNumber + 1;
            }
        }

        // Format: 001/BAST/WOTO/XII/2025
        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '/BAST/WOTO/' . $romanMonth . '/' . $currentYear;
    }

    public function editHandover($handoverId)
    {
        $handover = VehicleHandover::findOrFail($handoverId);

        // Check if handover belongs to this vehicle
        if ($handover->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $this->editingHandoverId = $handoverId;
        $this->handover_number = $handover->handover_number;
        $this->handover_date = $handover->handover_date ? date('Y-m-d', strtotime($handover->handover_date)) : '';
        $this->handover_from = $handover->handover_from;
        $this->handover_to = $handover->handover_to;
        $this->handover_from_address = $handover->handover_from_address ?? '';
        $this->handover_to_address = $handover->handover_to_address ?? '';
        $this->handover_transferee = $handover->transferee;
        $this->handover_receiving_party = $handover->receiving_party;

        $this->showEditHandoverModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeEditHandoverModal()
    {
        $this->showEditHandoverModal = false;
        $this->editingHandoverId = null;
        $this->resetHandoverForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function updateHandover()
    {
        $this->validate([
            'handover_date' => 'required|date',
            'handover_from' => 'required|string|max:255',
            'handover_to' => 'required|string|max:255',
            'handover_from_address' => 'required|string|max:60',
            'handover_to_address' => 'required|string|max:60',
            'handover_transferee' => 'required|string|max:255',
            'handover_receiving_party' => 'required|string|max:255',
        ], [
            'handover_date.required' => 'Tanggal serah terima harus diisi.',
            'handover_date.date' => 'Format tanggal tidak valid.',
            'handover_from.required' => 'Serah terima dari harus diisi.',
            'handover_from.max' => 'Serah terima dari maksimal 255 karakter.',
            'handover_to.required' => 'Kepada harus diisi.',
            'handover_to.max' => 'Kepada maksimal 255 karakter.',
            'handover_from_address.required' => 'Alamat dari harus diisi.',
            'handover_from_address.max' => 'Alamat dari maksimal 60 karakter.',
            'handover_to_address.required' => 'Alamat kepada harus diisi.',
            'handover_to_address.max' => 'Alamat kepada maksimal 60 karakter.',
            'handover_transferee.required' => 'Yang menyerahkan harus diisi.',
            'handover_transferee.max' => 'Yang menyerahkan maksimal 255 karakter.',
            'handover_receiving_party.required' => 'Yang menerima harus diisi.',
            'handover_receiving_party.max' => 'Yang menerima maksimal 255 karakter.',
        ]);

        $handover = VehicleHandover::findOrFail($this->editingHandoverId);

        // Check if handover belongs to this vehicle
        if ($handover->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $oldHandover = $handover->toArray();

        // Update handover data
        $handover->update([
            'handover_date' => $this->handover_date,
            'handover_from' => $this->handover_from,
            'handover_to' => $this->handover_to,
            'handover_from_address' => $this->handover_from_address,
            'handover_to_address' => $this->handover_to_address,
            'transferee' => $this->handover_transferee,
            'receiving_party' => $this->handover_receiving_party,
        ]);

        // Reload vehicle with handovers
        $this->vehicle->load('vehicleHandovers');

        // Log the update activity
        activity()
            ->performedOn($handover)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldHandover,
                'attributes' => [
                    'handover_number' => $this->handover_number,
                    'handover_date' => $this->handover_date,
                    'handover_from' => $this->handover_from,
                    'handover_to' => $this->handover_to,
                    'handover_from_address' => $this->handover_from_address,
                    'handover_to_address' => $this->handover_to_address,
                    'transferee' => $this->handover_transferee,
                    'receiving_party' => $this->handover_receiving_party,
                ]
            ])
            ->log('updated vehicle handover');

        // Show success message
        session()->flash('message', 'Berita Acara Serah Terima Kendaraan berhasil diperbarui.');

        // Close modal
        $this->closeEditHandoverModal();
    }

    public function deleteHandover($handoverId)
    {
        $handover = VehicleHandover::findOrFail($handoverId);

        // Check if handover belongs to this vehicle
        if ($handover->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $oldHandover = $handover->toArray();

        // Delete file if exists
        if ($handover->handover_file) {
            Storage::disk('public')->delete('documents/handovers/' . $handover->handover_file);
        }

        $handover->delete();

        // Reload vehicle with handovers
        $this->vehicle->load('vehicleHandovers');

        // Log the deletion activity
        activity()
            ->performedOn($handover)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldHandover,
            ])
            ->log('deleted vehicle handover');

        // Show success message
        session()->flash('message', 'Berita Acara Serah Terima Kendaraan berhasil dihapus.');
    }

    public function printHandover($handoverId)
    {
        try {
            $handover = VehicleHandover::with(['vehicle.brand', 'vehicle.type', 'vehicle.salesman'])->findOrFail($handoverId);

            // Check permissions
            if (!auth()->user()->can('vehicle-handover.print')) {
                abort(403, 'Unauthorized');
            }

            // Check if handover belongs to this vehicle
            if ($handover->vehicle_id !== $this->vehicle->id) {
                abort(403, 'Unauthorized');
            }

            // Update print count
            if ($handover->print_count == 0) {
                $handover->update(['printed_at' => now()]);
            }
            $handover->increment('print_count');

            // Generate PDF
            $pdf = Pdf::loadView('exports.berita-acara-serah-terima', compact('handover'));

            // Set PDF options for better formatting
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

            // Log the print activity
            activity()
                ->performedOn($handover)
                ->causedBy(auth()->user())
                ->withProperties(['print_count' => $handover->print_count])
                ->log('printed vehicle handover');

            // Return PDF download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'Berita_Acara_Serah_Terima_' . str_replace(['/', '\\'], '_', $handover->handover_number) . '.pdf');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to print handover', [
                'error' => $e->getMessage(),
                'handover_id' => $handoverId,
                'user_id' => auth()->id(),
            ]);

            // Show error message
            session()->flash('error', 'Gagal mencetak berita acara serah terima.');
            return redirect()->back();
        }
    }

    public function uploadHandoverFile($handoverId)
    {
        $handover = VehicleHandover::findOrFail($handoverId);

        // Check if handover belongs to this vehicle
        if ($handover->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $this->uploadingHandoverId = $handoverId;
        $this->handover_file = [];
        $this->showUploadHandoverModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeUploadHandoverModal()
    {
        $this->showUploadHandoverModal = false;
        $this->uploadingHandoverId = null;
        $this->handover_file = [];
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function uploadHandoverDocument()
    {
        $this->validate([
            'handover_file' => 'required|array|max:5',
            'handover_file.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'handover_file.required' => 'File berita acara harus dipilih.',
            'handover_file.array' => 'File berita acara harus berupa array.',
            'handover_file.max' => 'Maksimal 5 file yang dapat diupload.',
            'handover_file.*.required' => 'File berita acara harus dipilih.',
            'handover_file.*.file' => 'File berita acara harus berupa file.',
            'handover_file.*.mimes' => 'File berita acara harus berupa file PDF, JPG, JPEG, atau PNG.',
            'handover_file.*.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $handover = VehicleHandover::findOrFail($this->uploadingHandoverId);

        // Check if handover belongs to this vehicle
        if ($handover->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        // Handle file upload
        $fileNames = [];
        if ($this->handover_file) {
            foreach ($this->handover_file as $file) {
                if ($file && is_object($file)) { // Make sure it's a valid file object
                    $storedPath = $file->store('documents/handovers', 'public');
                    $fileNames[] = basename($storedPath);
                }
            }
        }

        if (!empty($fileNames)) {
            $documentPath = implode(',', $fileNames);

            // Store old file paths for cleanup
            $oldFiles = $handover->handover_file ? explode(',', $handover->handover_file) : [];

            // Update handover with file path
            $handover->update([
                'handover_file' => $documentPath,
            ]);

            // Delete old files if they exist
            foreach ($oldFiles as $oldFile) {
                Storage::disk('public')->delete('documents/handovers/' . trim($oldFile));
            }

            // Reload vehicle with handovers
            $this->vehicle->load('vehicleHandovers');

            // Log the upload activity
            activity()
                ->performedOn($handover)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'handover_file' => $documentPath,
                    ]
                ])
                ->log('uploaded handover document');

            // Show success message
            session()->flash('message', 'File berita acara serah terima berhasil diupload.');

            // Close modal
            $this->closeUploadHandoverModal();
        }
    }

    public function uploadRegistrationCertificateReceiptFile($certificateReceiptId)
    {
        $certificateReceipt = VehicleCertificateReceipt::findOrFail($certificateReceiptId);

        // Check if certificate receipt belongs to this vehicle
        if ($certificateReceipt->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $this->uploadingCertificateReceiptId = $certificateReceiptId;
        $this->certificate_receipt_file = [];
        $this->showUploadCertificateReceiptModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeUploadCertificateReceiptModal()
    {
        $this->showUploadCertificateReceiptModal = false;
        $this->uploadingCertificateReceiptId = null;
        $this->certificate_receipt_file = [];
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function uploadCertificateReceiptDocument()
    {
        $this->validate([
            'certificate_receipt_file' => 'required|array|max:5',
            'certificate_receipt_file.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'certificate_receipt_file.required' => 'File tanda terima BPKB harus dipilih.',
            'certificate_receipt_file.array' => 'File tanda terima BPKB harus berupa array.',
            'certificate_receipt_file.max' => 'Maksimal 5 file yang dapat diupload.',
            'certificate_receipt_file.*.required' => 'File tanda terima BPKB harus dipilih.',
            'certificate_receipt_file.*.file' => 'File tanda terima BPKB harus berupa file.',
            'certificate_receipt_file.*.mimes' => 'File tanda terima BPKB harus berupa file PDF, JPG, JPEG, atau PNG.',
            'certificate_receipt_file.*.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $certificateReceipt = VehicleCertificateReceipt::findOrFail($this->uploadingCertificateReceiptId);

        // Check if certificate receipt belongs to this vehicle
        if ($certificateReceipt->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        // Handle file upload
        $fileNames = [];
        if ($this->certificate_receipt_file) {
            foreach ($this->certificate_receipt_file as $file) {
                if ($file && is_object($file)) { // Make sure it's a valid file object
                    $storedPath = $file->store('documents/registration-certificate-receipts', 'public');
                    $fileNames[] = basename($storedPath);
                }
            }
        }

        if (!empty($fileNames)) {
            $documentPath = implode(',', $fileNames);

            // Store old file paths for cleanup
            $oldFiles = $certificateReceipt->receipt_file ? explode(',', $certificateReceipt->receipt_file) : [];

            // Update certificate receipt with file path
            $certificateReceipt->update([
                'receipt_file' => $documentPath,
            ]);

            // Delete old files if they exist
            foreach ($oldFiles as $oldFile) {
                Storage::disk('public')->delete('documents/registration-certificate-receipts/' . trim($oldFile));
            }

            // Reload vehicle with certificate receipts
            $this->vehicle->load('vehicleCertificateReceipts');

            // Log the upload activity
            activity()
                ->performedOn($certificateReceipt)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'receipt_file' => $documentPath,
                    ]
                ])
                ->log('uploaded certificate receipt document');

            // Show success message
            session()->flash('message', 'File tanda terima BPKB berhasil diupload.');

            // Close modal
            $this->closeUploadCertificateReceiptModal();
        }
    }

    // Vehicle File Modal Methods
    public function openFileModal()
    {
        $this->resetFileForm();
        $this->showFileModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function closeFileModal()
    {
        $this->showFileModal = false;
        $this->resetFileForm();
        $this->resetValidation();
        $this->resetErrorBag();
    }

    private function resetFileForm()
    {
        $this->vehicle_file_title_id = '';
        $this->vehicle_file = [];
        $this->editingVehicleFileId = null;
    }

    public function saveVehicleFile()
    {
        $this->validate([
            'vehicle_file_title_id' => 'required|exists:vehicle_file_titles,id',
            'vehicle_file' => 'required|array|max:5',
            'vehicle_file.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max per file
        ], [
            'vehicle_file_title_id.required' => 'Title file harus dipilih.',
            'vehicle_file_title_id.exists' => 'Title file tidak valid.',
            'vehicle_file.required' => 'File harus dipilih.',
            'vehicle_file.array' => 'File harus berupa array.',
            'vehicle_file.max' => 'Maksimal 5 file yang dapat diupload.',
            'vehicle_file.*.required' => 'File harus dipilih.',
            'vehicle_file.*.file' => 'File harus berupa file yang valid.',
            'vehicle_file.*.mimes' => 'File harus berupa PDF, JPG, JPEG, PNG, DOC, atau DOCX.',
            'vehicle_file.*.max' => 'Ukuran file maksimal 5MB.',
        ]);

        // Handle multiple file uploads
        $documentPath = null;
        if ($this->vehicle_file) {
            $fileNames = [];

            // Always treat as array since we use multiple attribute
            $files = (array) $this->vehicle_file;

            foreach ($files as $file) {
                if ($file && is_object($file)) { // Make sure it's a valid file object
                    $storedPath = $file->store('documents/vehicle-files', 'public');
                    $fileNames[] = basename($storedPath);
                }
            }

            if (!empty($fileNames)) {
                $documentPath = implode(',', $fileNames);
            }
        }

        if ($this->editingVehicleFileId) {
            // Update existing vehicle file
            $vehicleFile = VehicleFile::findOrFail($this->editingVehicleFileId);

            // Check if vehicle file belongs to this vehicle
            if ($vehicleFile->vehicle_id !== $this->vehicle->id) {
                abort(403, 'Unauthorized');
            }

            // Store old data for logging
            $oldVehicleFile = $vehicleFile->toArray();

            // Delete old files if new files are uploaded
            if ($documentPath && $vehicleFile->file_path) {
                $oldFileNames = explode(',', $vehicleFile->file_path);
                foreach ($oldFileNames as $oldFileName) {
                    Storage::disk('public')->delete('documents/vehicle-files/' . trim($oldFileName));
                }
            }

            // Update vehicle file record
            $vehicleFile->update([
                'vehicle_file_title_id' => $this->vehicle_file_title_id,
                'file_path' => $documentPath ?: $vehicleFile->file_path, // Keep old files if no new files uploaded
            ]);

            // Log the update activity
            activity()
                ->performedOn($vehicleFile)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldVehicleFile,
                    'attributes' => [
                        'vehicle_file_title_id' => $this->vehicle_file_title_id,
                        'file_path' => $vehicleFile->file_path,
                    ]
                ])
                ->log('updated vehicle file');

            $action = 'diperbarui';
        } else {
            // Create new vehicle file record
            $vehicleFile = VehicleFile::create([
                'vehicle_id' => $this->vehicle->id,
                'vehicle_file_title_id' => $this->vehicle_file_title_id,
                'file_path' => $documentPath,
                'created_by' => Auth::id(),
            ]);

            // Log the creation activity
            activity()
                ->performedOn($vehicleFile)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'vehicle_id' => $this->vehicle->id,
                        'vehicle_file_title_id' => $this->vehicle_file_title_id,
                        'file_path' => $documentPath,
                    ]
                ])
                ->log('created vehicle file');

            $action = 'ditambahkan';
        }

        // Reload vehicle with vehicle files
        $this->vehicle->load('vehicleFiles');

        // Show success message
        $fileCount = $this->vehicle_file ? count((array) $this->vehicle_file) : 0;
        if ($this->editingVehicleFileId) {
            session()->flash('message', 'File kendaraan berhasil diperbarui.');
        } else {
            session()->flash('message', $fileCount > 1 ? "{$fileCount} file kendaraan berhasil ditambahkan." : 'File kendaraan berhasil ditambahkan.');
        }

        // Close modal
        $this->closeFileModal();
    }

    public function editVehicleFile($vehicleFileId)
    {
        $vehicleFile = VehicleFile::findOrFail($vehicleFileId);

        // Check if vehicle file belongs to this vehicle
        if ($vehicleFile->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $this->editingVehicleFileId = $vehicleFileId;
        $this->vehicle_file_title_id = $vehicleFile->vehicle_file_title_id;
        // Note: We can't restore the original files for editing, so we leave vehicle_file empty
        // Users will need to re-upload files if they want to change them

        $this->showFileModal = true;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function deleteVehicleFile($vehicleFileId)
    {
        $vehicleFile = VehicleFile::findOrFail($vehicleFileId);

        // Check if vehicle file belongs to this vehicle
        if ($vehicleFile->vehicle_id !== $this->vehicle->id) {
            abort(403, 'Unauthorized');
        }

        // Store old file paths for cleanup
        $oldFilePaths = [];
        if ($vehicleFile->file_path) {
            $fileNames = explode(',', $vehicleFile->file_path);
            foreach ($fileNames as $fileName) {
                $oldFilePaths[] = 'documents/vehicle-files/' . trim($fileName);
            }
        }

        $oldVehicleFile = $vehicleFile->toArray();

        // Delete files from storage
        foreach ($oldFilePaths as $filePath) {
            Storage::disk('public')->delete($filePath);
        }

        $vehicleFile->delete();

        // Reload vehicle with vehicle files
        $this->vehicle->load('vehicleFiles');

        // Log the deletion activity
        activity()
            ->performedOn($vehicleFile)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldVehicleFile,
            ])
            ->log('deleted vehicle file');

        // Show success message
        session()->flash('message', 'File kendaraan berhasil dihapus.');
    }

    public function printPaymentReceipt($paymentReceiptId)
    {
        try {
            $paymentReceipt = PaymentReceipt::with(['vehicle.brand', 'vehicle.type', 'vehicle.salesman'])->findOrFail($paymentReceiptId);

            // Check permissions
            if (!auth()->user()->can('vehicle-payment-receipt.print')) {
                abort(403, 'Unauthorized');
            }

            // Update print count
            if ($paymentReceipt->print_count == 0) {
                $paymentReceipt->update(['printed_at' => now()]);
            }
            $paymentReceipt->increment('print_count');

            // Generate PDF
            $pdf = Pdf::loadView('exports.kwitansi', compact('paymentReceipt'));

            // Set PDF options for better formatting
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

            // Log the print activity
            activity()
                ->performedOn($paymentReceipt)
                ->causedBy(auth()->user())
                ->withProperties(['print_count' => $paymentReceipt->print_count])
                ->log('printed payment receipt');

            // Return PDF download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'Kwitansi_' . str_replace(['/', '\\'], '_', $paymentReceipt->payment_number) . '.pdf');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to print payment receipt', [
                'error' => $e->getMessage(),
                'payment_receipt_id' => $paymentReceiptId,
                'user_id' => auth()->id(),
            ]);

            // Show error message
            session()->flash('error', 'Gagal mencetak kwitansi penerimaan pembayaran.');
            return redirect()->back();
        }
    }

    public function printRegistrationCertificateReceipt($certificateReceiptId)
    {
        try {
            $certificateReceipt = VehicleCertificateReceipt::with(['vehicle.brand', 'vehicle.type', 'vehicle.salesman'])->findOrFail($certificateReceiptId);

            // Check permissions
            if (!auth()->user()->can('vehicle-registration-certificate-receipt.print')) {
                abort(403, 'Unauthorized');
            }

            // Check if certificate receipt belongs to this vehicle
            if ($certificateReceipt->vehicle_id !== $this->vehicle->id) {
                abort(403, 'Unauthorized');
            }

            // Update print count
            if ($certificateReceipt->print_count == 0) {
                $certificateReceipt->update(['printed_at' => now()]);
            }
            $certificateReceipt->increment('print_count');

            // Generate PDF
            $pdf = Pdf::loadView('exports.tanda-terima-bpkb', compact('certificateReceipt'));

            // Set PDF options for better formatting
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

            // Log the print activity
            activity()
                ->performedOn($certificateReceipt)
                ->causedBy(auth()->user())
                ->withProperties(['print_count' => $certificateReceipt->print_count])
                ->log('printed vehicle certificate receipt');

            // Return PDF download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'Tanda_Terima_BPKB_' . str_replace(['/', '\\'], '_', $certificateReceipt->certificate_receipt_number) . '.pdf');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to print certificate receipt', [
                'error' => $e->getMessage(),
                'certificate_receipt_id' => $certificateReceiptId,
                'user_id' => auth()->id(),
            ]);

            // Show error message
            session()->flash('error', 'Gagal mencetak tanda terima BPKB.');
            return redirect()->back();
        }
    }
}
