<?php

namespace App\Traits;

use App\Models\Config;
use Illuminate\Support\Facades\Auth;

trait HasWahaConfig
{
    /**
     * Get WAHA configuration for the current user
     *
     * @return Config|null
     */
    protected function getWahaConfig(): ?Config
    {
        return Config::where('user_id', Auth::id())->first();
    }

    /**
     * Get WAHA API URL for the current user
     *
     * @return string|null
     */
    protected function getWahaApiUrl(): ?string
    {
        $config = $this->getWahaConfig();
        return $config?->api_url;
    }

    /**
     * Get WAHA API Key for the current user
     *
     * @return string|null
     */
    protected function getWahaApiKey(): ?string
    {
        $config = $this->getWahaConfig();
        return $config?->api_key;
    }

    /**
     * Check if WAHA is configured for the current user
     *
     * @return bool
     */
    protected function isWahaConfigured(): bool
    {
        $config = $this->getWahaConfig();
        return $config && $config->api_url && $config->api_key;
    }
}

