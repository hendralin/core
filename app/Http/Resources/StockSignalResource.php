<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\StockSignal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StockSignal
 */
final class StockSignalResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_emiten' => $this->kode_emiten,
            // 'signal_type' => $this->signal_type,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),

            'market_cap' => $this->market_cap,
            'pbv' => $this->pbv,
            'per' => $this->per,

            // 'before_date' => $this->before_date?->format('Y-m-d'),
            // 'before_value' => $this->before_value,
            // 'before_close' => $this->before_close,
            // 'before_volume' => $this->before_volume,

            // 'hit_date' => $this->hit_date?->format('Y-m-d'),
            // 'hit_value' => $this->hit_value,
            // 'hit_close' => $this->hit_close,
            // 'hit_volume' => $this->hit_volume,

            // 'after_date' => $this->after_date?->format('Y-m-d'),
            // 'after_value' => $this->after_value,
            // 'after_close' => $this->after_close,
            // 'after_volume' => $this->after_volume,

            'notes' => $this->notes,
            // 'recommendation' => $this->recommendation,

            'stock_company' => $this->whenLoaded('stockCompany', function () {
                return [
                    'kode_emiten' => $this->stockCompany->kode_emiten ?? $this->kode_emiten,
                    'nama_emiten' => $this->stockCompany->nama_emiten ?? null,
                    'logo_url' => $this->stockCompany->logo_url ?? null,
                ];
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

