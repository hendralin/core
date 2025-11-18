<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CompanyEdit extends Component
{
    use WithFileUploads;
    public $company;
    public $logo;

    public string $name;
    public string $email;
    public string $phone;
    public string $address;
    public string $website;
    public string $tax_id;
    public string $description;
    public array $socialMedia = [];
    public string $newSocialPlatform = '';
    public string $newSocialUrl = '';
    public string $autoSaveStatus = '';
    public bool $hasUnsavedChanges = false;

    // License fields
    public string $license_key = '';
    public string $license_type = 'trial';
    public string $license_status = 'active';
    public string $license_issued_at = '';
    public string $license_expires_at = '';
    public string $max_users = '';
    public string $max_storage_gb = '';
    public array $features_enabled = [];
    public string $newFeature = '';
    public bool $showLicenseSection = false;

    public function mount(): void
    {
        $this->company = Company::first();

        $this->name = $this->company->name;
        $this->email = $this->company->email;
        $this->phone = $this->company->phone;
        $this->address = $this->company->address;
        $this->website = $this->company->website ?? '';
        $this->tax_id = $this->company->tax_id ?? '';
        $this->description = $this->company->description ?? '';
        $this->socialMedia = $this->company->social_media ?? [];

        // Initialize license fields
        $this->license_key = $this->company->license_key ?? '';
        $this->license_type = $this->company->license_type ?? 'trial';
        $this->license_status = $this->company->license_status ?? 'active';
        $this->license_issued_at = $this->company->license_issued_at ? $this->company->license_issued_at->format('Y-m-d') : '';
        $this->license_expires_at = $this->company->license_expires_at ? $this->company->license_expires_at->format('Y-m-d') : '';
        $this->max_users = $this->company->max_users ? (string)$this->company->max_users : '';
        $this->max_storage_gb = $this->company->max_storage_gb ? (string)$this->company->max_storage_gb : '';
        $this->features_enabled = $this->company->features_enabled ?? [];

        // Check for existing draft
        $draft = session('company_draft');
        if ($draft && isset($draft['saved_at'])) {
            $savedAt = \Carbon\Carbon::parse($draft['saved_at']);
            if ($savedAt->diffInMinutes(now()) < 60) { // Only restore if less than 1 hour old
                $this->autoSaveStatus = 'Draft available - click restore to load';
            }
        }
    }

    public function submit()
    {
        try {
        $this->validate([
            'name' => 'required|string|max:255',
                'email' => 'required|email:rfc,dns|max:255',
                'phone' => 'required|string|max:50|regex:/^[0-9+\-\s()]+$/',
            'address' => 'required|string',
                'website' => 'nullable|url|max:255',
                'tax_id' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:1000',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'socialMedia' => 'nullable|array',
                'socialMedia.*.platform' => 'required_with:socialMedia|string|max:50',
                'socialMedia.*.url' => 'required_with:socialMedia|url|max:255',
                // License validation
                'license_key' => 'nullable|string|max:255|unique:companies,license_key,' . $this->company->id,
                'license_type' => 'required|in:trial,basic,premium,enterprise',
                'license_status' => 'required|in:active,expired,suspended,cancelled',
                'license_issued_at' => 'nullable|date|before_or_equal:today',
                'license_expires_at' => 'nullable|date|after:license_issued_at',
                'max_users' => 'nullable|integer|min:1',
                'max_storage_gb' => 'nullable|integer|min:1',
                'features_enabled' => 'nullable|array',
            ]);

            // Handle logo upload with error handling
            $logoPath = null;
            if ($this->logo) {
                try {
                    // Delete old logo if exists
                    if ($this->company->logo && Storage::disk('logos')->exists($this->company->logo)) {
                        if (!Storage::disk('logos')->delete($this->company->logo)) {
                            Log::warning('Failed to delete old logo', [
                                'company_id' => $this->company->id,
                                'logo_path' => $this->company->logo
                            ]);
                        }
                    }

                    $logoPath = $this->logo->store('', 'logos');
                    if (!$logoPath) {
                        throw new \Exception('Failed to upload logo file');
                    }
                } catch (\Exception $e) {
                    Log::error('Logo upload failed', [
                        'company_id' => $this->company->id,
                        'error' => $e->getMessage()
                    ]);
                    session()->flash('error', 'Failed to upload logo. Please try again.');
                    return;
                }
            }

            // Update company with comprehensive error handling
            $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
                'website' => $this->website ?: null,
                'tax_id' => $this->tax_id ?: null,
                'description' => $this->description ?: null,
                'logo' => $logoPath ?: $this->company->logo,
                'social_media' => !empty($this->socialMedia) ? $this->socialMedia : null,
                // License fields
                'license_key' => $this->license_key ?: null,
                'license_type' => $this->license_type,
                'license_status' => $this->license_status,
                'license_issued_at' => $this->license_issued_at ?: null,
                'license_expires_at' => $this->license_expires_at ?: null,
                'max_users' => $this->max_users ? (int)$this->max_users : null,
                'max_storage_gb' => $this->max_storage_gb ? (int)$this->max_storage_gb : null,
                'features_enabled' => !empty($this->features_enabled) ? $this->features_enabled : null,
            ];

            if (!$this->company->update($updateData)) {
                throw new \Exception('Database update failed');
            }

            // Clear draft after successful save
            session()->forget('company_draft');
            $this->hasUnsavedChanges = false;
            $this->autoSaveStatus = '';

            // Log successful update with user context
            activity()
                ->performedOn($this->company)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $this->company->getOriginal(),
                    'new' => $this->company->getAttributes(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Company information updated');

            Log::info('Company updated successfully', [
                'company_id' => $this->company->id ?? null,
                'updated_by' => Auth::check() ? Auth::id() : 'system'
            ]);

            session()->flash('success', 'Company Info updated successfully.');

            return $this->redirect('/company?updated=true', true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are handled automatically by Livewire
            $this->addError('general', 'Please check the form for errors and try again.');
        } catch (\Exception $e) {
            Log::error('Company update failed', [
                'company_id' => $this->company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Something went wrong while saving. Please try again or contact support if the problem persists.');
        }
    }

    public function removeLogo()
    {
        if ($this->logo) {
            // Remove newly uploaded file (temporary)
            $this->logo = null;
            session()->flash('success', 'New logo upload cancelled.');
        } elseif ($this->company->logo && Storage::disk('logos')->exists($this->company->logo)) {
            // Remove existing logo from storage and database
            Storage::disk('logos')->delete($this->company->logo);
            $this->company->update(['logo' => null]);
            $this->company->refresh();
            session()->flash('success', 'Company logo removed successfully.');
        }
    }

    public function addSocialMedia()
    {
        try {
            $this->validate([
                'newSocialPlatform' => 'required|string|max:50',
                'newSocialUrl' => 'required|url|max:255',
            ]);

            // Check if platform already exists
            foreach ($this->socialMedia as $social) {
                if (strtolower($social['platform']) === strtolower($this->newSocialPlatform)) {
                    $this->addError('newSocialPlatform', 'This social media platform is already added.');
                    return;
                }
            }

            $this->socialMedia[] = [
                'platform' => $this->newSocialPlatform,
                'url' => $this->newSocialUrl,
            ];

            $this->newSocialPlatform = '';
            $this->newSocialUrl = '';
            $this->hasUnsavedChanges = true;
            $this->autoSaveStatus = 'Unsaved changes';

            session()->flash('success', 'Social media link added successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to add social media', [
                'company_id' => $this->company->id,
                'platform' => $this->newSocialPlatform,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to add social media link. Please try again.');
        }
    }

    public function removeSocialMedia($index)
    {
        try {
            if (isset($this->socialMedia[$index])) {
                $platform = $this->socialMedia[$index]['platform'] ?? 'Unknown';
                unset($this->socialMedia[$index]);
                $this->socialMedia = array_values($this->socialMedia); // Reindex array
                $this->hasUnsavedChanges = true;
                $this->autoSaveStatus = 'Unsaved changes';

                Log::info('Social media removed', [
                    'company_id' => $this->company->id,
                    'platform' => $platform
                ]);

                session()->flash('success', 'Social media link removed successfully.');
            } else {
                $this->addError('general', 'Invalid social media index.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to remove social media', [
                'company_id' => $this->company->id,
                'index' => $index,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to remove social media link. Please try again.');
        }
    }

    public function updated($property): void
    {
        // Skip auto-save for certain properties
        if (in_array($property, ['autoSaveStatus', 'hasUnsavedChanges', 'newSocialPlatform', 'newSocialUrl'])) {
            return;
        }

        $this->hasUnsavedChanges = true;
        $this->autoSaveStatus = 'Unsaved changes';

        // Auto-save draft after 3 seconds of inactivity
        $this->dispatch('auto-save-draft');
    }

    public function autoSaveDraft(): void
    {
        if (!$this->hasUnsavedChanges) {
            return;
        }

        try {
            // Save to session as draft
            session(['company_draft' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'website' => $this->website,
                'tax_id' => $this->tax_id,
                'description' => $this->description,
                'social_media' => $this->socialMedia,
                // License fields
                'license_key' => $this->license_key,
                'license_type' => $this->license_type,
                'license_status' => $this->license_status,
                'license_issued_at' => $this->license_issued_at,
                'license_expires_at' => $this->license_expires_at,
                'max_users' => $this->max_users,
                'max_storage_gb' => $this->max_storage_gb,
                'features_enabled' => $this->features_enabled,
                'saved_at' => now(),
            ]]);

            $this->autoSaveStatus = 'Draft saved ' . now()->format('H:i:s');
            $this->hasUnsavedChanges = false;

        } catch (\Exception $e) {
            Log::error('Auto-save draft failed', [
                'company_id' => $this->company->id,
                'error' => $e->getMessage()
            ]);
            $this->autoSaveStatus = 'Auto-save failed';
        }
    }

    public function restoreDraft(): void
    {
        $draft = session('company_draft');

        if ($draft) {
            $this->name = $draft['name'] ?? $this->name;
            $this->email = $draft['email'] ?? $this->email;
            $this->phone = $draft['phone'] ?? $this->phone;
            $this->address = $draft['address'] ?? $this->address;
            $this->website = $draft['website'] ?? $this->website;
            $this->tax_id = $draft['tax_id'] ?? $this->tax_id;
            $this->description = $draft['description'] ?? $this->description;
            $this->socialMedia = $draft['social_media'] ?? $this->socialMedia;

            // Restore license fields
            $this->license_key = $draft['license_key'] ?? $this->license_key;
            $this->license_type = $draft['license_type'] ?? $this->license_type;
            $this->license_status = $draft['license_status'] ?? $this->license_status;
            $this->license_issued_at = $draft['license_issued_at'] ?? $this->license_issued_at;
            $this->license_expires_at = $draft['license_expires_at'] ?? $this->license_expires_at;
            $this->max_users = $draft['max_users'] ?? $this->max_users;
            $this->max_storage_gb = $draft['max_storage_gb'] ?? $this->max_storage_gb;
            $this->features_enabled = $draft['features_enabled'] ?? $this->features_enabled;

            $this->autoSaveStatus = 'Draft restored';
            session()->flash('success', 'Draft restored successfully.');
        }
    }

    // License management methods
    public function toggleLicenseSection()
    {
        $this->showLicenseSection = !$this->showLicenseSection;
    }

    public function generateLicenseKey()
    {
        $this->license_key = 'LIC-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)) . '-' . date('Y');
        $this->hasUnsavedChanges = true;
        $this->autoSaveStatus = 'Unsaved changes';
        session()->flash('success', 'New license key generated.');
    }

    public function addFeature()
    {
        try {
            $this->validate([
                'newFeature' => 'required|string|max:100',
            ]);

            // Check if feature already exists
            if (in_array(strtolower($this->newFeature), array_map('strtolower', $this->features_enabled))) {
                $this->addError('newFeature', 'This feature is already added.');
                return;
            }

            $this->features_enabled[] = $this->newFeature;
            $this->newFeature = '';
            $this->hasUnsavedChanges = true;
            $this->autoSaveStatus = 'Unsaved changes';

            session()->flash('success', 'Feature added successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to add feature', [
                'company_id' => $this->company->id,
                'feature' => $this->newFeature,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to add feature. Please try again.');
        }
    }

    public function removeFeature($index)
    {
        try {
            if (isset($this->features_enabled[$index])) {
                $feature = $this->features_enabled[$index];
                unset($this->features_enabled[$index]);
                $this->features_enabled = array_values($this->features_enabled); // Reindex array
                $this->hasUnsavedChanges = true;
                $this->autoSaveStatus = 'Unsaved changes';

                Log::info('Feature removed', [
                    'company_id' => $this->company->id,
                    'feature' => $feature
                ]);

                session()->flash('success', 'Feature removed successfully.');
            } else {
                $this->addError('general', 'Invalid feature index.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to remove feature', [
                'company_id' => $this->company->id,
                'index' => $index,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to remove feature. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.company.company-edit');
    }
}
