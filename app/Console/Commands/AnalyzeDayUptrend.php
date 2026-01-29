<?php

namespace App\Console\Commands;

use App\Models\StockSignal;
use App\Models\TradingInfo;
use App\Models\FinancialRatio;
use Illuminate\Console\Command;

class AnalyzeDayUptrend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:analyze-day-uptrend
                            {--limit= : Batasi jumlah kode emiten yang ditampilkan}
                            {--from-latest=3 : Jumlah hari trading terakhir yang dianalisa (default 3)}
                            {--save : Simpan hasil sebagai stock signals}
                            {--publish : Auto-publish sinyal (hanya jika --save)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menganalisa pergerakan harga saham yang mengalami kenaikan terus menerus selama N hari trading terakhir (default 3 hari) menggunakan data dari tabel trading_infos.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) ($this->option('from-latest') ?: 3);
        if ($days < 2) {
            $this->error('Jumlah hari minimal adalah 2.');
            return Command::FAILURE;
        }

        $this->info("🔍 Mencari saham yang naik {$days} hari berturut-turut berdasarkan data TradingInfo...");

        // 1. Ambil N tanggal trading terakhir dari tabel trading_infos (global, bukan per saham)
        $dates = TradingInfo::query()
            ->select('date')
            ->distinct()
            ->orderByDesc('date')
            ->limit($days)
            ->pluck('date')
            ->toArray();

        if (count($dates) < $days) {
            $this->error("Data tanggal trading kurang dari {$days} hari. Tidak bisa melakukan analisa.");
            return Command::FAILURE;
        }

        // Urutkan dari paling lama ke terbaru
        sort($dates);

        $this->info('Periode analisa: ' . $dates[0]->format('Y-m-d') . ' s/d ' . end($dates)->format('Y-m-d'));

        // 2. Ambil data harga untuk tanggal-tanggal tersebut
        $rows = TradingInfo::query()
            ->select('kode_emiten', 'date', 'close', 'change')
            ->whereIn('date', $dates)
            ->orderBy('kode_emiten')
            ->orderBy('date')
            ->get();

        if ($rows->isEmpty()) {
            $this->warn('Tidak ada data TradingInfo untuk tanggal-tanggal tersebut.');
            return Command::SUCCESS;
        }

        // 3. Susun data per emiten
        $grouped = $rows->groupBy('kode_emiten');

        $result = [];

        foreach ($grouped as $kodeEmiten => $items) {
            // Index per tanggal supaya gampang diakses
            $byDate = $items->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

            // Pastikan semua tanggal ada
            $hasAllDates = true;
            $closes = [];
            $changes = [];

            foreach ($dates as $d) {
                $dateKey = $d->format('Y-m-d');
                if (! isset($byDate[$dateKey])) {
                    $hasAllDates = false;
                    break;
                }
                $closes[] = (float) $byDate[$dateKey]->close;
                $changes[] = (float) $byDate[$dateKey]->change;
            }

            if (! $hasAllDates) {
                continue;
            }

            // 4. Cek apakah naik terus:
            //    - Versi ketat: close hari ini > close kemarin
            //    - Alternatif (lebih longgar): change > 0 untuk semua hari
            $isStrictUp = true;
            for ($i = 1; $i < count($closes); $i++) {
                if ($closes[$i] <= $closes[$i - 1]) {
                    $isStrictUp = false;
                    break;
                }
            }

            if (! $isStrictUp) {
                continue;
            }

            $result[] = [
                'kode_emiten'   => $kodeEmiten,
                'start_date'    => $dates[0]->format('Y-m-d'),
                'end_date'      => end($dates)->format('Y-m-d'),
                'start_close'   => $closes[0],
                'end_close'     => end($closes),
                'total_change'  => end($closes) - $closes[0],
            ];
        }

        if (empty($result)) {
            $this->warn("Tidak ada saham yang naik {$days} hari berturut-turut pada periode tersebut.");
            return Command::SUCCESS;
        }

        // Urutkan berdasarkan total kenaikan (desc)
        usort($result, function ($a, $b) {
            return $b['total_change'] <=> $a['total_change'];
        });

        // Optional limit
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        if ($limit && $limit > 0) {
            $result = array_slice($result, 0, $limit);
        }

        // Simpan ke database jika diminta
        if ($this->option('save')) {
            $this->saveToDatabase($result, $days);
        }

        // Tampilkan dalam bentuk tabel
        $headers = [
            'Kode',
            'Dari Tanggal',
            'Sampai Tanggal',
            'Close Awal',
            'Close Akhir',
            'Total Kenaikan',
        ];

        $rowsTable = array_map(function ($row) {
            return [
                $row['kode_emiten'],
                $row['start_date'],
                $row['end_date'],
                number_format($row['start_close'], 2, ',', '.'),
                number_format($row['end_close'], 2, ',', '.'),
                number_format($row['total_change'], 2, ',', '.'),
            ];
        }, $result);

        $this->table($headers, $rowsTable);

        $this->info('✅ Selesai. Ditemukan ' . count($result) . ' saham yang naik ' . $days . ' hari berturut-turut.');

        return Command::SUCCESS;
    }

    /**
     * Simpan hasil analisa ke tabel stock_signals
     */
    protected function saveToDatabase(array $results, int $days): void
    {
        $this->info("💾 Menyimpan " . count($results) . " sinyal ke database...");

        $saved = 0;
        $skipped = 0;
        $autoPublish = $this->option('publish');

        foreach ($results as $row) {
            try {
                // Ambil data TradingInfo pada hari pertama (start_date) dan terakhir (end_date)
                $beforeInfo = TradingInfo::query()
                    ->where('kode_emiten', $row['kode_emiten'])
                    ->whereDate('date', $row['start_date'])
                    ->orderByDesc('date')
                    ->first();

                $hitInfo = TradingInfo::query()
                    ->where('kode_emiten', $row['kode_emiten'])
                    ->whereDate('date', $row['end_date'])
                    ->orderByDesc('date')
                    ->first();

                $marketCap = null;
                if ($hitInfo && !is_null($hitInfo->close) && !is_null($hitInfo->listed_shares)) {
                    $marketCap = (float) $hitInfo->close * (float) $hitInfo->listed_shares;
                }

                // Ambil rasio keuangan terbaru (PBV & PER) seperti di AnalyzeStockValueBreakthrough
                $ratio = FinancialRatio::query()
                    ->where('code', $row['kode_emiten'])
                    ->orderByDesc('fs_date')
                    ->first();

                $pbv = $ratio?->price_bv;
                $per = $ratio?->per;

                // Cek apakah sinyal serupa sudah ada (berdasarkan kode + end_date + jenis sinyal)
                $existing = StockSignal::where('kode_emiten', $row['kode_emiten'])
                    ->where('hit_date', $row['end_date'])
                    ->where('signal_type', 'day_uptrend')
                    ->first();

                if ($existing) {
                    $this->warn("Sinyal untuk {$row['kode_emiten']} pada {$row['end_date']} sudah ada, lewati...");
                    $skipped++;
                    continue;
                }

                $signal = StockSignal::create([
                    'signal_type'   => 'day_uptrend',
                    'kode_emiten'   => $row['kode_emiten'],

                    // Market cap & rasio keuangan
                    'market_cap'    => $marketCap,
                    'pbv'           => $pbv,
                    'per'           => $per,

                    // H-1: awal uptrend (pakai data TradingInfo start_date)
                    'before_date'   => $row['start_date'],
                    'before_value'  => $beforeInfo?->value,
                    'before_close'  => $beforeInfo?->close ?? $row['start_close'],
                    'before_volume' => $beforeInfo?->volume,

                    // H: akhir uptrend (hari ke-N) (pakai data TradingInfo end_date)
                    'hit_date'      => $row['end_date'],
                    'hit_value'     => $hitInfo?->value,
                    'hit_close'     => $hitInfo?->close ?? $row['end_close'],
                    'hit_volume'    => $hitInfo?->volume,

                    // Status
                    'status'        => $autoPublish ? 'published' : 'draft',
                    'published_at'  => $autoPublish ? now() : null,

                    // Catatan / rekomendasi sederhana
                    'recommendation' => $this->generateRecommendation($row, $days),
                ]);

                $saved++;
                $this->info("✓ Sinyal tersimpan untuk {$row['kode_emiten']} ({$row['end_date']})");

            } catch (\Exception $e) {
                $this->error("Gagal menyimpan sinyal untuk {$row['kode_emiten']}: {$e->getMessage()}");
            }
        }

        $this->info("✅ Penyimpanan selesai. Baru: {$saved}, Terlewat (sudah ada): {$skipped}.");
    }

    /**
     * Buat rekomendasi teks sederhana untuk sinyal ini
     */
    protected function generateRecommendation(array $row, int $days): string
    {
        $percent = $row['start_close'] > 0
            ? ($row['total_change'] / $row['start_close']) * 100
            : 0;

        $percentFormatted = number_format($percent, 2, ',', '.');

        return "Saham {$row['kode_emiten']} naik {$days} hari berturut-turut dari " .
            "{$row['start_date']} (close {$row['start_close']}) " .
            "hingga {$row['end_date']} (close {$row['end_close']}), " .
            "total kenaikan sekitar {$percentFormatted}%.";
    }
}

