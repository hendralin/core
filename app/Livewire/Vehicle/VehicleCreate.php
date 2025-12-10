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
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Models\VehicleEquipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

#[Title('Create Vehicle')]
class VehicleCreate extends Component
{
    use WithFileUploads;

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
    public $loan_price;
    public $roadside_allowance;
    public $selling_date;
    public $selling_price;
    public $display_price;
    public $salesman_id;
    public $status = '1';
    public $description;

    public $bpkb_number;
    public $bpkb_file;

    public $images = []; // Array of uploaded images
    public $tempImages = []; // Temporary storage for new images

    // Vehicle equipment completeness
    public $stnk_asli = true; // STNK asli
    public $kunci_roda = false; // Kunci roda
    public $ban_serep = false; // Ban serep
    public $kunci_serep = false; // Kunci serep
    public $dongkrak = false; // Dongkrak

    public $showResetModal = false;

    // Progress indicator properties
    public $progress_percentage = 0;
    public $current_step = ['step' => 1, 'name' => 'Informasi Dasar'];

    public function updateProgress()
    {
        // Calculate total required fields based on status
        $baseFields = 15; // Basic (6) + Technical (3) + Registration (6)
        $totalFields = $baseFields + 5; // Financial (5) + Status (always required)

        // Add selling fields if status is sold
        if ($this->status == '0') {
            $totalFields += 3; // selling_date, selling_price, salesman_id
        }

        // Images are optional, so we don't add to totalFields but count if uploaded

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

        // Registration (6 required fields)
        if ($this->warehouse_id) $filledFields++;
        if ($this->vehicle_registration_date) $filledFields++;
        if ($this->vehicle_registration_expiry_date) $filledFields++;
        if ($this->file_stnk) $filledFields++;
        if ($this->bpkb_number) $filledFields++;
        if ($this->bpkb_file) $filledFields++;

        // Financial (5 required fields)
        if ($this->purchase_date) $filledFields++;
        if ($this->purchase_price) $filledFields++;
        if ($this->display_price) $filledFields++;
        if ($this->loan_price) $filledFields++;
        if ($this->roadside_allowance) $filledFields++;

        // Status is always required
        $filledFields++; // Status is always counted as filled

        // Selling info (3 fields, only if status is sold)
        if ($this->status == '0') {
            if ($this->selling_date) $filledFields++;
            if ($this->selling_price) $filledFields++;
            if ($this->salesman_id) $filledFields++;
        }

        // Images are optional - add bonus progress if uploaded
        if (!empty($this->images)) {
            $imageBonus = min(2, count($this->images)); // Max 2 bonus points for images
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
        'police_number' => 'required|string|max:11|unique:vehicles,police_number|regex:/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/',
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
        'file_stnk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'warehouse_id' => 'required|exists:warehouses,id',
        'purchase_date' => 'required|date|before_or_equal:today',
        'purchase_price' => 'required|numeric|min:0|max:99999999999999.99',
        'selling_date' => 'required_if:status,0|nullable|date|after_or_equal:purchase_date',
        'selling_price' => 'required_if:status,0|nullable|numeric|min:0|max:99999999999999.99',
        'display_price' => 'required|numeric|min:0|max:99999999999999.99',
        'loan_price' => 'required|numeric|min:0|max:99999999999999.99',
        'roadside_allowance' => 'required|numeric|min:0|max:99999999999999.99',
        'salesman_id' => 'required_if:status,0|nullable|exists:salesmen,id',
        'status' => 'required|in:0,1',
        'description' => 'nullable|string',
        'bpkb_number' => 'required|string|max:255|unique:vehicles,bpkb_number',
        'bpkb_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // Max 5MB per image
        'images' => 'nullable|array|max:10', // Max 10 images
    ];

    protected $messages = [
        'police_number.required' => 'Nomor Polisi wajib diisi.',
        'police_number.unique' => 'Nomor Polisi sudah ada.',
        'police_number.regex' => 'Nomor Polisi harus dalam format: XX 1234 ABC (e.g., BG 1821 MY, B 188 UN, BG 1234 ABC).',
        'brand_id.required' => 'Brand wajib diisi.',
        'type_id.required' => 'Type wajib diisi.',
        'category_id.required' => 'Category wajib diisi.',
        'vehicle_model_id.required' => 'Model wajib diisi.',
        'year.required' => 'Tahun wajib diisi.',
        'chassis_number.required' => 'Nomor Rangka wajib diisi.',
        'engine_number.required' => 'Nomor Mesin wajib diisi.',
        'kilometer.required' => 'Kilometer wajib diisi.',
        'vehicle_registration_date.required' => 'Tanggal Registrasi wajib diisi.',
        'vehicle_registration_date.after_or_equal' => 'Tanggal Registrasi tidak boleh di masa lalu.',
        'vehicle_registration_expiry_date.required' => 'Tanggal Kadaluarsa wajib diisi.',
        'vehicle_registration_expiry_date.after' => 'Tanggal Kadaluarsa tidak boleh di masa lalu.',
        'file_stnk.required' => 'File STNK wajib diisi.',
        'warehouse_id.required' => 'Warehouse wajib diisi.',
        'purchase_date.required' => 'Tanggal Pembelian wajib diisi.',
        'purchase_date.before_or_equal' => 'Tanggal Pembelian tidak boleh di masa lalu.',
        'purchase_price.required' => 'Harga Pembelian wajib diisi.',
        'selling_date.after_or_equal' => 'Tanggal Penjualan tidak boleh di masa lalu.',
        'selling_date.required_if' => 'Tanggal Penjualan wajib diisi.',
        'selling_price.required_if' => 'Harga Penjualan wajib diisi.',
        'display_price.required' => 'Harga Jual wajib diisi.',
        'salesman_id.required_if' => 'Salesman wajib diisi untuk kendaraan yang terjual.',
        'salesman_id.exists' => 'Salesman yang dipilih tidak valid.',
        'file_stnk.mimes' => 'File STNK harus dalam format PDF, JPG, JPEG, atau PNG.',
        'file_stnk.max' => 'File STNK tidak boleh lebih dari 2MB.',
        'fuel_type.in' => 'Jenis Bahan Bakar harus diisi dengan Bensin atau Solar.',
        'images.*.image' => 'File harus berupa gambar.',
        'images.*.mimes' => 'Format gambar harus JPEG, JPG, PNG, GIF, atau WebP.',
        'images.*.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
        'images.array' => 'Format upload gambar tidak valid.',
        'images.max' => 'Maksimal 10 gambar yang dapat diupload.',
        'bpkb_number.required' => 'Nomor BPKB wajib diisi.',
        'bpkb_number.unique' => 'Nomor BPKB sudah ada.',
        'bpkb_file.required' => 'File BPKB wajib diisi.',
        'bpkb_file.mimes' => 'File BPKB harus dalam format PDF, JPG, JPEG, atau PNG.',
        'bpkb_file.max' => 'File BPKB tidak boleh lebih dari 2MB.',
    ];

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

    public function updatedLoanPrice()
    {
        $this->loan_price = $this->formatNumber($this->loan_price);
        $this->updateProgress();
    }

    public function updatedRoadsideAllowance()
    {
        $this->roadside_allowance = $this->formatNumber($this->roadside_allowance);
        $this->updateProgress();
    }

    public function updatedCylinderCapacity()
    {
        $this->cylinder_capacity = $this->formatNumber($this->cylinder_capacity);
        $this->updateProgress();
    }

    public function updatedImages()
    {
        $this->updateProgress();
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images); // Reindex array
            $this->updateProgress();

            // Dispatch event to update accumulated files in JavaScript
            $this->dispatch('image-removed', index: $index);
        }
    }

    public function addImages($files)
    {
        // Convert files to temporary uploads and add to existing images
        $currentImages = $this->images ?? [];

        // Filter valid files
        $validFiles = array_filter($files, function($file) {
            return in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])
                && $file->getSize() <= 5 * 1024 * 1024; // 5MB limit
        });

        // Check total count limit
        if (count($currentImages) + count($validFiles) > 10) {
            $this->addError('images', 'Maksimal 10 gambar yang dapat diupload.');
            return;
        }

        // Add new files to existing images
        $this->images = array_merge($currentImages, array_values($validFiles));

        $this->updateProgress();
    }

    public function syncAccumulatedFiles($fileCount)
    {
        // This method is called from JavaScript to sync accumulated files count
        // We use it to validate that JavaScript and PHP are in sync
        $currentCount = count($this->images ?? []);
        if ($fileCount !== $currentCount) {
            // If there's a mismatch, we can log it or handle it
            // For now, we'll just log it
            Log::info("File count mismatch: JS reports {$fileCount}, PHP has {$currentCount}");
        }
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


    public function updatedStatus()
    {
        $this->updateProgress();
    }

    public function confirmReset()
    {
        $this->showResetModal = true;
    }

    public function resetForm()
    {
        $this->reset([
            'police_number', 'year', 'brand_id', 'type_id', 'category_id', 'vehicle_model_id',
            'chassis_number', 'engine_number', 'cylinder_capacity', 'color', 'fuel_type',
            'kilometer', 'warehouse_id', 'vehicle_registration_date', 'vehicle_registration_expiry_date',
            'file_stnk', 'bpkb_number', 'bpkb_file', 'purchase_date', 'purchase_price', 'selling_date', 'selling_price',
            'display_price', 'loan_price', 'roadside_allowance', 'salesman_id', 'status', 'description', 'images', 'tempImages',
            'stnk_asli', 'kunci_roda', 'ban_serep', 'kunci_serep', 'dongkrak'
        ]);

        $this->status = 1; // Reset to default status

        // Reset equipment defaults
        $this->stnk_asli = true;
        $this->kunci_roda = false;
        $this->ban_serep = false;
        $this->kunci_serep = false;
        $this->dongkrak = false;

        $this->showResetModal = false;

        // Clear validation errors
        $this->resetValidation();

        // Clear any flash messages
        session()->forget(['success', 'error', 'info', 'warning']);

        // Dispatch event to clear Quill editor
        $this->dispatch('clear-quill-editor');

        // Also dispatch browser event as fallback
        $this->dispatch('clear-quill', to: 'browser');

        // Update progress indicator - force reset to initial state
        $this->progress_percentage = 0;
        $this->current_step = ['step' => 1, 'name' => 'Informasi Dasar'];

        session()->flash('info', 'Form telah direset.');
    }

    public function cancelReset()
    {
        $this->showResetModal = false;
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
        // Parse formatted values before validation
        if ($this->kilometer) {
            $this->kilometer = $this->parseFormatted($this->kilometer);
        }
        if ($this->purchase_price) {
            $this->purchase_price = $this->parseFormatted($this->purchase_price);
        }
        if ($this->selling_price) {
            $this->selling_price = $this->parseFormatted($this->selling_price);
        }
        if ($this->loan_price) {
            $this->loan_price = $this->parseFormatted($this->loan_price);
        }
        if ($this->roadside_allowance) {
            $this->roadside_allowance = $this->parseFormatted($this->roadside_allowance);
        }
        if ($this->cylinder_capacity) {
            $this->cylinder_capacity = $this->parseFormatted($this->cylinder_capacity);
        }
        if ($this->display_price) {
            $this->display_price = $this->parseFormatted($this->display_price);
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
        $fileStnkPath = null;
        if ($this->file_stnk) {
            $storedPath = $this->file_stnk->store('photos/stnk', 'public');
            $fileStnkPath = basename($storedPath);
        }

        $fileBpkbPath = null;
        if ($this->bpkb_file) {
            $storedPath = $this->bpkb_file->store('photos/bpkb', 'public');
            $fileBpkbPath = basename($storedPath);
        }

        $vehicleImages = [];
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                if ($image) {
                    $storedImagePath = $image->store('photos/vehicles', 'public');
                    $vehicleImages[] = basename($storedImagePath);
                }
            }
        }

        try {
            DB::beginTransaction();

            // Create vehicle record
            $vehicle = Vehicle::create([
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
                'bpkb_number' => $this->bpkb_number,
                'bpkb_file' => $fileBpkbPath,
                'warehouse_id' => $this->warehouse_id,
                'salesman_id' => $this->salesman_id,
                'purchase_date' => $this->purchase_date,
                'purchase_price' => $this->purchase_price,
                'display_price' => $this->display_price,
                'loan_price' => $this->loan_price,
                'roadside_allowance' => $this->roadside_allowance,
                'selling_date' => $this->selling_date,
                'selling_price' => $this->selling_price,
                'status' => $this->status,
                'description' => $this->description,
            ]);

            // Create vehicle image records
            foreach ($vehicleImages as $imagePath) {
                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image' => $imagePath,
                ]);
            }

            // Create vehicle equipment record
            VehicleEquipment::create([
                'type' => 2, // 2 = purchase equipment
                'vehicle_id' => $vehicle->id,
                'stnk_asli' => (int) $this->stnk_asli,
                'kunci_roda' => (int) $this->kunci_roda,
                'ban_serep' => (int) $this->ban_serep,
                'kunci_serep' => (int) $this->kunci_serep,
                'dongkrak' => (int) $this->dongkrak,
            ]);

            // Log the creation activity with detailed information
            activity()
                ->performedOn($vehicle)
                ->causedBy(Auth::user())
                ->withProperties([
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
                        'bpkb_number' => $this->bpkb_number,
                        'bpkb_file' => $fileBpkbPath,
                        'warehouse_id' => $this->warehouse_id,
                        'salesman_id' => $this->salesman_id,
                        'purchase_date' => $this->purchase_date,
                        'purchase_price' => $this->purchase_price,
                        'selling_date' => $this->selling_date,
                        'selling_price' => $this->selling_price,
                        'display_price' => $this->display_price,
                        'status' => $this->status,
                        'description' => $this->description,
                    ]
                ])
                ->log('created vehicle');

            DB::commit();

            session()->flash('success', 'Vehicle created.');
            return $this->redirect('/vehicles', true);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Failed to create vehicle', [
                'error' => $e->getMessage(),
                'police_number' => $this->police_number,
                'user_id' => Auth::id(),
            ]);

            // Clean up uploaded files if transaction failed
            if ($fileStnkPath && file_exists(storage_path('app/public/photos/stnk/' . $fileStnkPath))) {
                unlink(storage_path('app/public/photos/stnk/' . $fileStnkPath));
            }

            if ($fileBpkbPath && file_exists(storage_path('app/public/photos/bpkb/' . $fileBpkbPath))) {
                unlink(storage_path('app/public/photos/bpkb/' . $fileBpkbPath));
            }

            foreach ($vehicleImages as $imagePath) {
                if (file_exists(storage_path('app/public/photos/vehicles/' . $imagePath))) {
                    unlink(storage_path('app/public/photos/vehicles/' . $imagePath));
                }
            }

            session()->flash('error', 'Failed to create vehicle. Please try again.');
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

        return view('livewire.vehicle.vehicle-create', compact('brands', 'types', 'categories', 'models', 'warehouses', 'salesmen'));
    }
}
