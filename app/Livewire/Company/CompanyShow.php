<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Company Info')]
class CompanyShow extends Component
{
    public $name, $address, $phone, $email, $company;
    public $website, $tax_id, $description;
    public $completenessPercentage = 0;
    public $completenessTips = [];
    public $socialMedia = [];

    // License information
    public $licenseInfo = [];
    public $isLicenseExpiringSoon = false;
    public $daysUntilExpiration = null;

    public function mount(): void
    {
        $this->company = Company::first();

        $this->name = $this->company->name;
        $this->address = $this->company->address;
        $this->phone = $this->company->phone;
        $this->email = $this->company->email;
        $this->website = $this->company->website;
        $this->tax_id = $this->company->tax_id;
        $this->description = $this->company->description;
        $this->socialMedia = $this->company->social_media ?? [];

        // Initialize license information
        $this->licenseInfo = [
            'license_key' => $this->company->license_key,
            'license_type' => $this->company->license_type,
            'license_status' => $this->company->license_status,
            'license_issued_at' => $this->company->license_issued_at,
            'license_expires_at' => $this->company->license_expires_at,
            'max_users' => $this->company->max_users,
            'max_storage_gb' => $this->company->max_storage_gb,
            'features_enabled' => $this->company->features_enabled,
            'is_active' => $this->company->isLicenseActive(),
            'is_expired' => $this->company->isLicenseExpired(),
            'is_trial' => $this->company->isTrialLicense(),
            'status_color' => $this->company->getLicenseStatusColor(),
            'type_display' => $this->company->getLicenseTypeDisplay(),
        ];

        $this->daysUntilExpiration = $this->company->getDaysUntilExpiration();
        $this->isLicenseExpiringSoon = $this->company->isLicenseExpiringSoon();

        $this->calculateCompleteness();
    }

    public function getActivities()
    {
        return $this->company->activities()
            ->with('causer')
            ->latest()
            ->take(10)
            ->get();
    }

    public function updated($property): void
    {
        // Recalculate completeness when company data changes
        if (in_array($property, ['company'])) {
            $this->calculateCompleteness();
        }
    }

    public function calculateCompleteness(): void
    {
        $fields = [
            'name' => $this->company->name,
            'email' => $this->company->email,
            'phone' => $this->company->phone,
            'address' => $this->company->address,
            'logo' => $this->company->logo,
            'website' => $this->company->website,
            'tax_id' => $this->company->tax_id,
            'description' => $this->company->description,
            'social_media' => !empty($this->company->social_media) ? json_encode($this->company->social_media) : null,
            // License fields
            'license_type' => $this->company->license_type,
            'license_status' => $this->company->license_status,
            'license_expires_at' => $this->company->license_expires_at,
        ];

        $totalFields = count($fields);
        $filledFields = count(array_filter($fields, function($value) {
            return !empty($value) && $value !== null;
        }));

        $this->completenessPercentage = round(($filledFields / $totalFields) * 100);

        // Generate tips for missing fields
        $this->completenessTips = [];
        if (empty($this->company->logo)) {
            $this->completenessTips[] = 'Add a company logo to make your profile more professional';
        }
        if (empty($this->company->website)) {
            $this->completenessTips[] = 'Add your company website to provide more information';
        }
        if (empty($this->company->tax_id)) {
            $this->completenessTips[] = 'Add tax ID for official documentation';
        }
        if (empty($this->company->description)) {
            $this->completenessTips[] = 'Add a company description to tell others about your business';
        }
        if (empty($this->company->social_media)) {
            $this->completenessTips[] = 'Add social media links to increase your online presence';
        }
        if (empty($this->company->license_type) || $this->company->license_type === 'trial') {
            $this->completenessTips[] = 'Set up a proper license type for your company';
        }
        if (empty($this->company->license_expires_at)) {
            $this->completenessTips[] = 'Set license expiration date to track your subscription';
        }
    }

    public function render()
    {
        return view('livewire.company.company-show');
    }
}
