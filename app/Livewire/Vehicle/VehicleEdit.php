<?php

namespace App\Livewire\Vehicle;

use App\Models\Type;
use App\Models\Brand;
use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Category;
use App\Models\Salesman;
use App\Models\Warehouse;
use App\Models\VehicleImage;
use App\Models\VehicleModel;
use App\Models\Leasing;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Models\VehicleEquipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Title('Edit Vehicle')]
class VehicleEdit extends Component
{
    use WithFileUploads;

    public Vehicle $vehicle;

    public $police_number;
    public $brand_id;
    public $type_id;
    public $category_id;
    public $vehicle_model_id;
    public $year;
    public $cylinder_capacity;
    public $chassis_number;
    public $engine_number;
    public $color;
    public $fuel_type;
    public $kilometer;
    public $vehicle_registration_date;
    public $vehicle_registration_expiry_date;
    public $file_stnk;
    public $warehouse_id;
    public $purchase_date;
    public $purchase_price;
    public $selling_date;
    public $selling_price;
    public $display_price;
    public $loan_price;
    public $roadside_allowance;
    public $salesman_id;
    public $buyer_name;
    public $buyer_phone;
    public $buyer_address;
    public $payment_type;
    public $leasing_id;
    public $status;
    public $description;

    public $existing_file_stnk;

    public $images = []; // Array of uploaded images
    public $tempImages = []; // Temporary storage for new images
    public $existingImages = []; // Existing images from database

    public $showResetModal = false;

    // Image management methods
    public $imagesToDelete = []; // Track images to delete

    // Vehicle equipment completeness
    public $stnk_asli = true; // STNK asli
    public $kunci_roda = false; // Kunci roda
    public $ban_serep = false; // Ban serep
    public $kunci_serep = false; // Kunci serep
    public $dongkrak = false; // Dongkrak

    // Progress indicator properties
    public $progress_percentage = 0;
    public $current_step = ['step' => 1, 'name' => 'Informasi Dasar'];

    public function updateProgress()
    {
        // Calculate total required fields based on status
        $baseFields = 13; // Basic (6) + Technical (3) + Registration (4)
        $totalFields = $baseFields + 5; // Financial (5) + Status (always required)

        // Add selling fields if status is sold
        if ($this->status == '0') {
            $totalFields += 8; // selling_date, selling_price, salesman_id, buyer_name, buyer_phone, buyer_address, payment_type, leasing_id
        }

        $filledFields = 0;

        // Basic Information (6 required fields)
        if ($this->police_number) $filledFields++;
        if ($this->year) $filledFields++;
        if ($this->brand_id) $filledFields++;
        if ($this->type_id) $filledFields++;
        if ($this->category_id) $filledFields++;
        if ($this->vehicle_model_id) $filledFields++;

        // Technical Details (3 required fields)
        if ($this->chassis_number) $filledFields++;
        if ($this->engine_number) $filledFields++;
        if ($this->kilometer) $filledFields++;

        // Registration (4 required fields)
        if ($this->warehouse_id) $filledFields++;
        if ($this->vehicle_registration_date) $filledFields++;
        if ($this->vehicle_registration_expiry_date) $filledFields++;
        if ($this->file_stnk || $this->existing_file_stnk) $filledFields++;

        // Financial (5 required fields)
        if ($this->purchase_date) $filledFields++;
        if ($this->purchase_price) $filledFields++;
        if ($this->display_price) $filledFields++;
        if ($this->loan_price) $filledFields++;
        if ($this->roadside_allowance) $filledFields++;

        // Status is always required
        $filledFields++; // Status is always counted as filled

        // Selling info (8 fields, only if status is sold)
        if ($this->status == '0') {
            if ($this->selling_date) $filledFields++;
            if ($this->selling_price) $filledFields++;
            if ($this->salesman_id) $filledFields++;
            if ($this->buyer_name) $filledFields++;
            if ($this->buyer_phone) $filledFields++;
            if ($this->buyer_address) $filledFields++;
            if ($this->payment_type) $filledFields++;
            if ($this->payment_type == '2' && $this->leasing_id) $filledFields++;
        }

        // Images are optional - add bonus progress if uploaded or existing
        $totalImages = count($this->images) + count($this->existingImages);
        if ($totalImages > 0) {
            $imageBonus = min(2, $totalImages); // Max 2 bonus points for images
            $totalFields += 2; // Add to total for progress calculation
            $filledFields += $imageBonus;
        }

        $this->progress_percentage = min(100, round(($filledFields / $totalFields) * 100));

        // Update current step based on percentage
        if ($this->progress_percentage < 25) {
            $this->current_step = ['step' => 1, 'name' => 'Informasi Dasar'];
        } elseif ($this->progress_percentage < 50) {
            $this->current_step = ['step' => 2, 'name' => 'Detail Kendaraan'];
        } elseif ($this->progress_percentage < 75) {
            $this->current_step = ['step' => 3, 'name' => 'Spesifikasi Teknis'];
        } elseif ($this->progress_percentage < 95) {
            $this->current_step = ['step' => 4, 'name' => 'Registrasi & Dokumen'];
        } else {
            $this->current_step = ['step' => 5, 'name' => 'Informasi Keuangan'];
        }
    }

    protected $rules = [
        'police_number' => 'required|string|max:11|regex:/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/',
        'brand_id' => 'required|exists:brands,id',
        'type_id' => 'required|exists:types,id',
        'category_id' => 'required|exists:categories,id',
        'vehicle_model_id' => 'required|exists:vehicle_models,id',
        'year' => 'required|integer',
        'cylinder_capacity' => 'nullable|numeric|min:0|max:99999999999999.99',
        'chassis_number' => 'required|string|max:255',
        'engine_number' => 'required|string|max:255',
        'color' => 'nullable|string|max:255',
        'fuel_type' => 'nullable|in:Bensin,Solar',
        'kilometer' => 'required|numeric|min:0|max:99999999999999.99',
        'vehicle_registration_date' => 'required|date|after_or_equal:today',
        'vehicle_registration_expiry_date' => 'required|date|after:vehicle_registration_date',
        'file_stnk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'warehouse_id' => 'required|exists:warehouses,id',
        'purchase_date' => 'required|date|before_or_equal:today',
        'purchase_price' => 'required|numeric|min:0|max:99999999999999.99',
        'selling_date' => 'required_if:status,0|nullable|date|after_or_equal:purchase_date',
        'selling_price' => 'required_if:status,0|nullable|numeric|min:0|max:99999999999999.99',
        'display_price' => 'required|numeric|min:0|max:99999999999999.99',
        'loan_price' => 'required|numeric|min:0|max:99999999999999.99',
        'roadside_allowance' => 'required|numeric|min:0|max:99999999999999.99',
        'salesman_id' => 'required_if:status,0|nullable|exists:salesmen,id',
        'buyer_name' => 'required_if:status,0|nullable|string|max:255',
        'buyer_phone' => 'required_if:status,0|nullable|string|max:20',
        'buyer_address' => 'required_if:status,0|nullable|string|max:1000',
        'payment_type' => 'required_if:status,0|nullable|in:1,2',
        'leasing_id' => 'required_if:payment_type,2|nullable|exists:leasings,id',
        'status' => 'required|in:0,1',
        'description' => 'nullable|string',
        'tempImages.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // Max 5MB per image
        'tempImages' => 'nullable|array|max:10', // Max 10 images
    ];

    protected $messages = [
        'police_number.required' => 'Police number is required.',
        'police_number.regex' => 'Police number must be in format: XX 1234 ABC (e.g., BG 1821 MY, B 188 UN, BG 1234 ABC).',
        'brand_id.required' => 'Brand is required.',
        'type_id.required' => 'Type is required.',
        'category_id.required' => 'Category is required.',
        'vehicle_model_id.required' => 'Model is required.',
        'year.required' => 'Year is required.',
        'chassis_number.required' => 'Chassis number is required.',
        'engine_number.required' => 'Engine number is required.',
        'kilometer.required' => 'Kilometer is required.',
        'vehicle_registration_date.required' => 'Vehicle registration date is required.',
        'vehicle_registration_date.date' => 'Vehicle registration date must be a valid date.',
        'vehicle_registration_date.after_or_equal' => 'Vehicle registration date cannot be in the past.',
        'vehicle_registration_expiry_date.required' => 'Vehicle registration expiry date is required.',
        'vehicle_registration_expiry_date.date' => 'Vehicle registration expiry date must be a valid date.',
        'vehicle_registration_expiry_date.after' => 'Vehicle registration expiry date must be after registration date.',
        'warehouse_id.required' => 'Warehouse is required.',
        'purchase_date.required' => 'Purchase date is required.',
        'purchase_date.before_or_equal' => 'Purchase date cannot be in the future.',
        'purchase_price.required' => 'Purchase price is required.',
        'selling_date.after_or_equal' => 'Selling date must be after or equal to purchase date.',
        'selling_price.required_if' => 'Selling price is required when status is Sold.',
        'selling_price.numeric' => 'Selling price must be a number.',
        'selling_price.min' => 'Selling price must be greater than 0.',
        'selling_price.max' => 'Selling price must be less than 99999999999999.99.',
        'salesman_id.required_if' => 'Salesman is required for sold vehicles.',
        'salesman_id.exists' => 'Selected salesman is invalid.',
        'buyer_name.required_if' => 'Nama pembeli harus diisi untuk kendaraan yang terjual.',
        'buyer_phone.required_if' => 'Nomor telepon pembeli harus diisi untuk kendaraan yang terjual.',
        'buyer_address.required_if' => 'Alamat pembeli harus diisi untuk kendaraan yang terjual.',
        'payment_type.required_if' => 'Metode pembayaran harus dipilih untuk kendaraan yang terjual.',
        'payment_type.in' => 'Metode pembayaran harus berupa Tunai atau Kredit.',
        'leasing_id.required_if' => 'Leasing harus dipilih jika metode pembayaran adalah Kredit.',
        'leasing_id.exists' => 'Leasing yang dipilih tidak valid.',
        'display_price.required' => 'Display price is required.',
        'display_price.numeric' => 'Display price must be a number.',
        'display_price.min' => 'Display price must be greater than 0.',
        'display_price.max' => 'Display price must be less than 99999999999999.99.',
        'selling_date.required_if' => 'Selling date is required when status is Sold.',
        'selling_date.date' => 'Selling date must be a valid date.',
        'selling_date.after_or_equal' => 'Selling date must be after or equal to purchase date.',
        'status.required' => 'Status is required.',
        'status.in' => 'Status must be either Sold or Available.',
        'file_stnk.mimes' => 'File STNK must be a PDF, JPG, JPEG, or PNG file.',
        'file_stnk.max' => 'File STNK size must not exceed 2MB.',
        'fuel_type.in' => 'Fuel type must be either Bensin or Solar.',
        'tempImages.*.image' => 'File must be an image.',
        'tempImages.*.mimes' => 'Image format must be JPEG, JPG, PNG, GIF, or WebP.',
        'tempImages.*.max' => 'Image size must not exceed 5MB.',
        'tempImages.array' => 'Invalid image upload format.',
        'tempImages.max' => 'Maximum 10 images can be uploaded.',
    ];

    public function mount(Vehicle $vehicle)
    {
        // Ensure images relationship is loaded
        $this->vehicle = $vehicle->load('images');

        // Populate form fields
        $this->police_number = $vehicle->police_number;
        $this->brand_id = $vehicle->brand_id;
        $this->type_id = $vehicle->type_id;
        $this->category_id = $vehicle->category_id;
        $this->vehicle_model_id = $vehicle->vehicle_model_id;
        $this->year = $vehicle->year;
        $this->cylinder_capacity = $vehicle->cylinder_capacity ? number_format($vehicle->cylinder_capacity, 0, ',', '.') : null ;
        $this->chassis_number = $vehicle->chassis_number;
        $this->engine_number = $vehicle->engine_number;
        $this->color = $vehicle->color;
        $this->fuel_type = $vehicle->fuel_type;
        $this->kilometer = number_format($vehicle->kilometer, 0, ',', '.');
        $this->purchase_date = $vehicle->purchase_date;
        $this->purchase_price = number_format($vehicle->purchase_price, 0, ',', '.');
        $this->display_price = number_format($vehicle->display_price, 0, ',', '.');
        $this->loan_price = $vehicle->loan_price ? number_format($vehicle->loan_price, 0, ',', '.') : null;
        $this->roadside_allowance = $vehicle->roadside_allowance ? number_format($vehicle->roadside_allowance, 0, ',', '.') : null;
        $this->vehicle_registration_date = $vehicle->vehicle_registration_date;
        $this->vehicle_registration_expiry_date = $vehicle->vehicle_registration_expiry_date;
        $this->existing_file_stnk = $vehicle->file_stnk;
        $this->warehouse_id = $vehicle->warehouse_id;
        $this->salesman_id = $vehicle->salesman_id;
        $this->buyer_name = $vehicle->buyer_name;
        $this->buyer_phone = $vehicle->buyer_phone;
        $this->buyer_address = $vehicle->buyer_address;
        $this->payment_type = $vehicle->payment_type;
        $this->leasing_id = $vehicle->leasing_id;
        $this->selling_date = $vehicle->selling_date;
        $this->selling_price = $vehicle->selling_price ? number_format($vehicle->selling_price, 0, ',', '.') : null;
        $this->status = $vehicle->status;
        $this->description = $vehicle->description;

        // Load existing images
        $this->existingImages = $vehicle->images ? $vehicle->images->map(function ($image) {
            return [
                'id' => $image->id,
                'image' => $image->image,
                'url' => asset('photos/vehicles/' . $image->image),
                'to_delete' => false
            ];
        })->toArray() : [];

        // Load existing vehicle equipment
        $equipment = $vehicle->equipment()->where('type', 2)->first(); // type 2 = purchase equipment
        if ($equipment) {
            $this->stnk_asli = (bool) $equipment->stnk_asli;
            $this->kunci_roda = (bool) $equipment->kunci_roda;
            $this->ban_serep = (bool) $equipment->ban_serep;
            $this->kunci_serep = (bool) $equipment->kunci_serep;
            $this->dongkrak = (bool) $equipment->dongkrak;
        }

        // Initialize progress indicator
        $this->updateProgress();
    }

    public function updatedBrandId()
    {
        $this->type_id = null;
        $this->vehicle_model_id = null;
        $this->updateProgress();
    }

    public function updatedPoliceNumber()
    {
        $this->police_number = $this->formatPoliceNumber($this->police_number);
        $this->updateProgress();
    }

    public function updatedKilometer()
    {
        $this->kilometer = $this->formatNumber($this->kilometer);
        $this->updateProgress();
    }

    public function updatedPurchasePrice()
    {
        $this->purchase_price = $this->formatNumber($this->purchase_price);
        $this->updateProgress();
    }

    public function updatedSellingPrice()
    {
        $this->selling_price = $this->formatNumber($this->selling_price);
        $this->updateProgress();
    }

    public function updatedDisplayPrice()
    {
        $this->display_price = $this->formatNumber($this->display_price);
        $this->updateProgress();
    }

    public function updatedCylinderCapacity()
    {
        $this->cylinder_capacity = $this->formatNumber($this->cylinder_capacity);
        $this->updateProgress();
    }

    public function updatedTempImages()
    {
        // When tempImages is updated, merge with existing images
        if (!empty($this->tempImages)) {
            if (!is_array($this->images)) {
                $this->images = [];
            }

            // Add new images to existing array
            foreach ($this->tempImages as $image) {
                if (count($this->images) < 10) { // Only add if under limit
                    $this->images[] = $image;
                }
            }

            // Clear temp images after merging
            $this->tempImages = [];

            $this->updateProgress();
        }
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images); // Reindex array
            $this->updateProgress();

            // Dispatch event to update UI
            $this->dispatch('image-removed', index: $index);
        }
    }

    public function removeExistingImage($imageId)
    {
        // Mark existing image for deletion
        $this->imagesToDelete[] = $imageId;

        // Remove from existingImages array
        $this->existingImages = array_filter($this->existingImages, function ($image) use ($imageId) {
            return $image['id'] != $imageId;
        });

        $this->updateProgress();

        $this->dispatch('existing-image-removed', imageId: $imageId);
    }

    public function updatedStatus()
    {
        $this->updateProgress();
    }

    public function updatedPaymentType()
    {
        // Clear leasing_id if payment type is not credit
        if ($this->payment_type != '2') {
            $this->leasing_id = null;
        }
        $this->updateProgress();
    }

    private function formatPoliceNumber($value)
    {
        // Remove all spaces and convert to uppercase
        $cleaned = strtoupper(preg_replace('/\s+/', '', $value));

        // Apply Indonesian police number format: XX 1234 ABC
        if (preg_match('/^([A-Z]{1,2})([0-9]{1,4})([A-Z]{0,3})$/', $cleaned, $matches)) {
            $province = $matches[1];
            $number = $matches[2];
            $area = $matches[3] ?? '';

            // Build formatted string
            $formatted = $province . ' ' . $number;
            if (!empty($area)) {
                $formatted .= ' ' . $area;
            }

            return $formatted;
        }

        return $value;
    }

    private function formatNumber($value)
    {
        // Remove all non-numeric characters except decimal point and comma
        $cleaned = preg_replace('/[^\d.,]/', '', $value);

        // Handle decimal part if present
        $parts = explode(',', $cleaned);
        $integerPart = $parts[0];
        $decimalPart = isset($parts[1]) ? ',' . $parts[1] : '';

        // Remove any existing thousand separators from integer part
        $integerPart = preg_replace('/[^\d]/', '', $integerPart);

        // Format with Indonesian thousand separator (period)
        if (!empty($integerPart)) {
            $integerPart = number_format((int)$integerPart, 0, '', '.');
        }

        return $integerPart . $decimalPart;
    }

    private function parseFormatted($value)
    {
        // Remove thousand separators (periods) and replace comma with dot for decimal
        $cleaned = str_replace('.', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }

    public function updatedTypeId()
    {
        $this->vehicle_model_id = null;
        $this->updateProgress();
    }

    public function submit()
    {
        // Update unique validation to exclude current vehicle
        $this->rules['police_number'] = 'required|string|max:11|unique:vehicles,police_number,' . $this->vehicle->id;

        // Parse formatted values before validation
        $this->kilometer = $this->parseFormatted($this->kilometer);
        $this->purchase_price = $this->parseFormatted($this->purchase_price);
        $this->display_price = $this->parseFormatted($this->display_price);
        $this->loan_price = $this->parseFormatted($this->loan_price);
        $this->roadside_allowance = $this->parseFormatted($this->roadside_allowance);
        if ($this->selling_price) {
            $this->selling_price = $this->parseFormatted($this->selling_price);
        }
        if ($this->cylinder_capacity) {
            $this->cylinder_capacity = $this->parseFormatted($this->cylinder_capacity);
        }

        $this->validate();

        // Additional year validation
        $currentYear = date('Y');
        $minYear = $currentYear - 15;
        $maxYear = $currentYear + 1;

        if ($this->year < $minYear || $this->year > $maxYear) {
            $this->addError('year', "Year must be between {$minYear} and {$maxYear}.");
            return;
        }

        // Handle file uploads before transaction (filesystem operations)
        $fileStnkPath = $this->vehicle->file_stnk;

        // Handle file upload
        if ($this->file_stnk) {
            // Delete old file if exists
            if ($this->vehicle->file_stnk && Storage::disk('public')->exists($this->vehicle->file_stnk)) {
                Storage::disk('public')->delete($this->vehicle->file_stnk);
            }

            $storedPath = $this->file_stnk->store('photos/stnk', 'public');
            $fileStnkPath = basename($storedPath);
        }

        // Handle new image uploads before transaction
        $uploadedImagePaths = [];
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                if ($image) {
                    $storedImagePath = $image->store('photos/vehicles', 'public');
                    $uploadedImagePaths[] = basename($storedImagePath);
                }
            }
        }

        try {
            DB::beginTransaction();

            $oldData = [
                'police_number' => $this->vehicle->police_number,
                'brand_id' => $this->vehicle->brand_id,
                'type_id' => $this->vehicle->type_id,
                'category_id' => $this->vehicle->category_id,
                'vehicle_model_id' => $this->vehicle->vehicle_model_id,
                'year' => $this->vehicle->year,
                'cylinder_capacity' => $this->vehicle->cylinder_capacity,
                'chassis_number' => $this->vehicle->chassis_number,
                'engine_number' => $this->vehicle->engine_number,
                'color' => $this->vehicle->color,
                'fuel_type' => $this->vehicle->fuel_type,
                'kilometer' => $this->vehicle->kilometer,
                'vehicle_registration_date' => $this->vehicle->vehicle_registration_date,
                'vehicle_registration_expiry_date' => $this->vehicle->vehicle_registration_expiry_date,
                'file_stnk' => $this->vehicle->file_stnk,
                'warehouse_id' => $this->vehicle->warehouse_id,
                'salesman_id' => $this->vehicle->salesman_id,
                'buyer_name' => $this->vehicle->buyer_name,
                'buyer_phone' => $this->vehicle->buyer_phone,
                'buyer_address' => $this->vehicle->buyer_address,
                'payment_type' => $this->vehicle->payment_type,
                'leasing_id' => $this->vehicle->leasing_id,
                'purchase_date' => $this->vehicle->purchase_date,
                'purchase_price' => $this->vehicle->purchase_price,
                'display_price' => $this->vehicle->display_price,
                'loan_price' => $this->vehicle->loan_price,
                'roadside_allowance' => $this->vehicle->roadside_allowance,
                'selling_date' => $this->vehicle->selling_date,
                'selling_price' => $this->vehicle->selling_price,
                'status' => $this->vehicle->status,
                'description' => $this->vehicle->description,
            ];

            // Update vehicle record
            $this->vehicle->update([
                'police_number' => $this->police_number,
                'brand_id' => $this->brand_id,
                'type_id' => $this->type_id,
                'category_id' => $this->category_id,
                'vehicle_model_id' => $this->vehicle_model_id,
                'year' => $this->year,
                'cylinder_capacity' => $this->cylinder_capacity,
                'chassis_number' => $this->chassis_number,
                'engine_number' => $this->engine_number,
                'color' => $this->color,
                'fuel_type' => $this->fuel_type,
                'kilometer' => $this->kilometer,
                'vehicle_registration_date' => $this->vehicle_registration_date,
                'vehicle_registration_expiry_date' => $this->vehicle_registration_expiry_date,
                'file_stnk' => $fileStnkPath,
                'warehouse_id' => $this->warehouse_id,
                'salesman_id' => $this->status == '0' ? $this->salesman_id : null,
                'buyer_name' => $this->status == '0' ? $this->buyer_name : null,
                'buyer_phone' => $this->status == '0' ? $this->buyer_phone : null,
                'buyer_address' => $this->status == '0' ? $this->buyer_address : null,
                'payment_type' => $this->status == '0' ? $this->payment_type : null,
                'leasing_id' => $this->status == '0' && $this->payment_type == '2' ? $this->leasing_id : null,
                'purchase_date' => $this->purchase_date,
                'purchase_price' => $this->purchase_price,
                'display_price' => $this->display_price,
                'loan_price' => $this->loan_price,
                'roadside_allowance' => $this->roadside_allowance,
                'selling_date' => $this->status == '0' ? $this->selling_date : null,
                'selling_price' => $this->status == '0' ? $this->selling_price : null,
                'status' => $this->status,
                'description' => $this->description,
            ]);

            // Handle image deletions
            if (!empty($this->imagesToDelete)) {
                foreach ($this->imagesToDelete as $imageId) {
                    $image = VehicleImage::find($imageId);
                    if ($image) {
                        // Delete file from storage
                        if (Storage::disk('public')->exists('photos/vehicles/' . $image->image)) {
                            Storage::disk('public')->delete('photos/vehicles/' . $image->image);
                        }
                        // Delete from database
                        $image->delete();
                    }
                }
            }

            // Create new vehicle image records
            foreach ($uploadedImagePaths as $imagePath) {
                VehicleImage::create([
                    'vehicle_id' => $this->vehicle->id,
                    'image' => $imagePath,
                ]);
            }

            // Update or create vehicle equipment record
            VehicleEquipment::updateOrCreate(
                [
                    'vehicle_id' => $this->vehicle->id,
                    'type' => 2, // 2 = purchase equipment
                ],
                [
                    'stnk_asli' => (int) $this->stnk_asli,
                    'kunci_roda' => (int) $this->kunci_roda,
                    'ban_serep' => (int) $this->ban_serep,
                    'kunci_serep' => (int) $this->kunci_serep,
                    'dongkrak' => (int) $this->dongkrak,
                ]
            );

            // Log the update activity with detailed information
            activity()
                ->performedOn($this->vehicle)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldData,
                    'attributes' => [
                        'police_number' => $this->police_number,
                        'brand_id' => $this->brand_id,
                        'type_id' => $this->type_id,
                        'category_id' => $this->category_id,
                        'vehicle_model_id' => $this->vehicle_model_id,
                        'year' => $this->year,
                        'cylinder_capacity' => $this->cylinder_capacity,
                        'chassis_number' => $this->chassis_number,
                        'engine_number' => $this->engine_number,
                        'color' => $this->color,
                        'fuel_type' => $this->fuel_type,
                        'kilometer' => $this->kilometer,
                        'vehicle_registration_date' => $this->vehicle_registration_date,
                        'vehicle_registration_expiry_date' => $this->vehicle_registration_expiry_date,
                        'file_stnk' => $fileStnkPath,
                        'warehouse_id' => $this->warehouse_id,
                        'salesman_id' => $this->status == '0' ? $this->salesman_id : null,
                        'buyer_name' => $this->status == '0' ? $this->buyer_name : null,
                        'buyer_phone' => $this->status == '0' ? $this->buyer_phone : null,
                        'buyer_address' => $this->status == '0' ? $this->buyer_address : null,
                        'payment_type' => $this->status == '0' ? $this->payment_type : null,
                        'leasing_id' => $this->status == '0' && $this->payment_type == '2' ? $this->leasing_id : null,
                        'purchase_date' => $this->purchase_date,
                        'purchase_price' => $this->purchase_price,
                        'display_price' => $this->display_price,
                        'loan_price' => $this->loan_price,
                        'roadside_allowance' => $this->roadside_allowance,
                        'selling_date' => $this->status == '0' ? $this->selling_date : null,
                        'selling_price' => $this->status == '0' ? $this->selling_price : null,
                        'status' => $this->status,
                        'description' => $this->description,
                        'uploaded_images' => $uploadedImagePaths,
                        'deleted_images' => $this->imagesToDelete,
                    ]
                ])
                ->log('updated vehicle');

            DB::commit();

            session()->flash('success', 'Vehicle updated.');
            return $this->redirect('/vehicles', true);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Failed to update vehicle', [
                'error' => $e->getMessage(),
                'vehicle_id' => $this->vehicle->id,
                'police_number' => $this->police_number,
                'user_id' => Auth::id(),
            ]);

            // Clean up uploaded files if transaction failed
            if ($this->file_stnk && $fileStnkPath !== $this->vehicle->file_stnk && file_exists(storage_path('app/public/photos/stnk/' . $fileStnkPath))) {
                unlink(storage_path('app/public/photos/stnk/' . $fileStnkPath));
            }

            foreach ($uploadedImagePaths as $imagePath) {
                if (file_exists(storage_path('app/public/photos/vehicles/' . $imagePath))) {
                    unlink(storage_path('app/public/photos/vehicles/' . $imagePath));
                }
            }

            session()->flash('error', 'Failed to update vehicle. Please try again.');
            return;
        }
    }

    public function render()
    {
        $brands = Brand::orderBy('name')->get();
        $types = Type::when($this->brand_id, fn($q) => $q->where('brand_id', $this->brand_id))
                     ->orderBy('name')
                     ->get();
        $categories = Category::orderBy('name')->get();
        $models = VehicleModel::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $salesmen = Salesman::orderBy('name')->get();
        $leasings = Leasing::orderBy('name')->get();

        return view('livewire.vehicle.vehicle-edit', compact('brands', 'types', 'categories', 'models', 'warehouses', 'salesmen', 'leasings'));
    }

    public function confirmReset()
    {
        $this->showResetModal = true;
    }

    public function resetForm()
    {
        // Reset all form fields to original vehicle data
        $this->fill($this->vehicle->toArray());

        // Handle specific fields that might need special formatting
        $this->kilometer = $this->formatNumber($this->vehicle->kilometer);
        $this->purchase_price = $this->formatNumber($this->vehicle->purchase_price);
        $this->display_price = $this->formatNumber($this->vehicle->display_price);
        $this->loan_price = $this->formatNumber($this->vehicle->loan_price);
        $this->roadside_allowance = $this->formatNumber($this->vehicle->roadside_allowance);
        $this->selling_price = $this->vehicle->selling_price ? $this->formatNumber($this->vehicle->selling_price) : null;
        $this->cylinder_capacity = $this->formatNumber($this->vehicle->cylinder_capacity);

        // Load buyer data
        $this->buyer_name = $this->vehicle->buyer_name;
        $this->buyer_phone = $this->vehicle->buyer_phone;
        $this->buyer_address = $this->vehicle->buyer_address;
        $this->payment_type = $this->vehicle->payment_type;
        $this->leasing_id = $this->vehicle->leasing_id;

        // Reset image properties
        $this->images = [];
        $this->tempImages = [];
        $this->existingImages = $this->vehicle->images ? $this->vehicle->images->map(function ($image) {
            return [
                'id' => $image->id,
                'image' => $image->image,
                'url' => asset('storage/photos/vehicles/' . $image->image),
                'to_delete' => false
            ];
        })->toArray() : [];
        $this->imagesToDelete = [];

        // Reset equipment properties to original values
        $equipment = $this->vehicle->equipment()->where('type', 2)->first();
        if ($equipment) {
            $this->stnk_asli = (bool) $equipment->stnk_asli;
            $this->kunci_roda = (bool) $equipment->kunci_roda;
            $this->ban_serep = (bool) $equipment->ban_serep;
            $this->kunci_serep = (bool) $equipment->kunci_serep;
            $this->dongkrak = (bool) $equipment->dongkrak;
        } else {
            // Default values if no equipment record exists
            $this->stnk_asli = true;
            $this->kunci_roda = false;
            $this->ban_serep = false;
            $this->kunci_serep = false;
            $this->dongkrak = false;
        }

        $this->showResetModal = false;

        // Clear validation errors
        $this->resetValidation();

        // Clear any flash messages
        session()->forget(['success', 'error', 'info', 'warning']);

        // Dispatch event to restore Quill editor content
        $this->dispatch('restore-quill-editor', description: $this->vehicle->description ?? '');

        session()->flash('info', 'Form telah direset ke data asli.');
    }

    public function cancelReset()
    {
        $this->showResetModal = false;
    }
}
