<?php

namespace App\Neuron\Agents;

use App\Neuron\BaseStockAgent;
use Illuminate\Support\Carbon;
use NeuronAI\Agent\SystemPrompt;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use App\Models\TradingInfo;
use App\Models\StockCompany;
use App\Services\News\NewsAggregatorService;

class ScreeningAgent extends BaseStockAgent
{
    protected function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                'You are a stock screening assistant for the Bandar Saham application.',
                'You work with Indonesia stock market data (IDX) and focus on bullish, bearish, or sideways conditions.',
                'Untuk berita emiten: gunakan tool get_emiten_news_headlines yang menggabungkan data internal (idx_news) dan opsional sumber live (Stockbit Snips API jika dikonfigurasi di server, plus Google News RSS); hasilnya berisi headline + URL. Sentimen negatif pada metadata hanyalah heuristik keyword, bukan kesimpulan investasi.',
            ],
            steps: [
                'Understand the user request about screening stocks (bullish, bearish, sideways) or optimizing filters/strategies.',
                'Jika user menyebut kode emiten spesifik (mis. PADI, BBCA), ambil datanya lewat tool by-code (bukan menebak dari screening umum).',
                'If you need fresh market data, use the available screening tool instead of guessing tickers.',
                'Analyze the returned candidates and classify them into bullish / bearish / watchlist with clear reasons based on the metrics.',
                'Jika user bertanya tentang berita (positif/negatif), kabar emiten, atau "ada berita buruk tidak?": WAJIB panggil tool get_emiten_news_headlines dengan kode emiten yang dimaksud (infer dari konteks jika jelas). Jelaskan hasil dengan menyebut headline dan tautan dari field items[].url. Jika hasil kosong, baru sarankan pengecekan manual lewat portal referensi di bawah.',
            ],
            output: [
                'Always answer in Indonesian unless the user explicitly uses another language.',
                'Explain your reasoning using concrete numbers (change %, volume, value, sector) from the provided data.',
                'When giving a list, limit to the most relevant 5–10 stocks and summarize the common pattern at the end.',
                'Untuk pertanyaan berita: tampilkan poin berisi headline + URL dari hasil tool. Jika tool kosong, berikan daftar portal referensi dengan URL lengkap. Jangan mengarang headline yang tidak ada di hasil tool. sentiment_hint hanyalah petunjuk; ingatkan verifikasi di pengumuman resmi BEI/OJK.',
            ],
            toolsUsage: [
                'Use the screening tool when you need to fetch or refresh candidates from the database.',
                'Jika user meminta data untuk kode emiten tertentu, gunakan tool get_trading_info_by_code dan jelaskan hasilnya dari time-series yang dikembalikan.',
                'Do not invent data that is not present in the tool results.',
                'Untuk berita emiten: selalu gunakan get_emiten_news_headlines terlebih dahulu bila user menyebut atau menyiratkan kode emiten.',
                'Referensi tambahan jika user butuh sumber lain (bukan menggantikan tool): Stockbit Snips https://snips.stockbit.com/ ; Berita IDX https://idx.co.id/id/berita/berita ; Kontan https://www.kontan.co.id/ ; CNBC Indonesia (market) https://www.cnbcindonesia.com/market ; Bisnis https://bisnis.com/ ; Investor ID https://investor.id/ ; RTI https://www.rti.co.id/ ; E-Info BEI https://www.idx.co.id/id/perusahaan-tercatat/informasi-perusahaan ; OJK https://www.ojk.go.id/ . Forum (opini): komunitas Stockbit, Reddit r/finansial — verifikasi silang.',
            ],
        );
    }

    /**
     * Define tools that the agent can use.
     */
    protected function tools(): array
    {
        return [
            Tool::make(
                'get_emiten_news_headlines',
                'Ambil headline berita terkait kode emiten (database idx_news + opsional Stockbit Snips + Google News RSS saat include_live). Mengembalikan judul, URL, sumber, dan sentiment_hint heuristik.'
            )
                ->addProperty(new ToolProperty(
                    name: 'code',
                    type: PropertyType::STRING,
                    description: 'Kode emiten (required), misalnya PADI.',
                    required: true
                ))
                ->addProperty(new ToolProperty(
                    name: 'limit',
                    type: PropertyType::INTEGER,
                    description: 'Jumlah maksimal item (default 10, maksimal 20).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'days',
                    type: PropertyType::INTEGER,
                    description: 'Rentang hari ke belakang untuk data internal (default 30, maksimal 365).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'negative_only',
                    type: PropertyType::BOOLEAN,
                    description: 'Jika true, hanya item dengan sentiment_hint negatif (heuristik keyword).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'include_live',
                    type: PropertyType::BOOLEAN,
                    description: 'Sertakan sumber live (Stockbit Snips jika tersedia + Google News RSS) selain database idx_news (default true).',
                    required: false
                ))
                ->setCallable(function (
                    string $code,
                    ?int $limit = null,
                    ?int $days = null,
                    ?bool $negative_only = null,
                    ?bool $include_live = null,
                ): array {
                    $limit = $limit !== null && $limit > 0 ? min($limit, 20) : 10;
                    $days = $days !== null && $days > 0 ? min($days, 365) : 30;
                    $negativeOnly = (bool) ($negative_only ?? false);
                    $includeLive = $include_live === null ? true : (bool) $include_live;

                    return app(NewsAggregatorService::class)->headlines(
                        $code,
                        $limit,
                        $days,
                        $includeLive,
                        $negativeOnly,
                    );
                }),

            Tool::make(
                'get_trading_info_by_code',
                'Ambil data trading (time-series) untuk satu kode emiten dari tabel trading_infos.'
            )
                ->addProperty(new ToolProperty(
                    name: 'code',
                    type: PropertyType::STRING,
                    description: 'Kode emiten (required), misalnya PADI.',
                    required: true
                ))
                ->addProperty(new ToolProperty(
                    name: 'days',
                    type: PropertyType::INTEGER,
                    description: 'Jumlah hari terakhir yang diambil (default 200, maksimal 365).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'end_date',
                    type: PropertyType::STRING,
                    description: 'Tanggal akhir (YYYY-MM-DD). Jika kosong gunakan tanggal terakhir tersedia di database.',
                    required: false
                ))
                ->setCallable(function (string $code, ?int $days = null, ?string $end_date = null): array {
                    $code = strtoupper(trim($code));
                    $days = $days && $days > 0 ? min($days, 365) : 200;

                    $end = $end_date
                        ? Carbon::parse($end_date)->startOfDay()
                        : (TradingInfo::max('date') ? Carbon::parse((string) TradingInfo::max('date'))->startOfDay() : null);

                    if (!$end) {
                        return [];
                    }

                    $start = (clone $end)->subDays($days);

                    $rows = TradingInfo::query()
                        ->with('stockCompany')
                        ->where('kode_emiten', $code)
                        ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                        ->orderBy('date')
                        ->get();

                    return $rows->map(function (TradingInfo $row) {
                        /** @var StockCompany|null $company */
                        $company = $row->stockCompany;

                        return [
                            'kode_emiten' => $row->kode_emiten,
                            'company_name' => $company?->nama_emiten,
                            'sector' => $company?->sektor,
                            'industry' => $company?->industri,
                            'date' => optional($row->date)->format('Y-m-d'),
                            'previous' => (float) $row->previous,
                            'open' => (float) $row->open_price,
                            'high' => (float) $row->high,
                            'low' => (float) $row->low,
                            'close' => (float) $row->close,
                            'change' => (float) $row->change,
                            'volume' => (float) $row->volume,
                            'value' => (float) $row->value,
                            'foreign_buy' => (float) $row->foreign_buy,
                            'foreign_sell' => (float) $row->foreign_sell,
                        ];
                    })->all();
                }),

            Tool::make(
                'run_stock_screening',
                'Jalankan screening saham berdasarkan arah tren (bullish/bearish) dan ambang volume/nilai dasar.'
            )
                ->addProperty(new ToolProperty(
                    name: 'direction',
                    type: PropertyType::STRING,
                    description: 'Arah tren yang diminta: "bullish", "bearish", atau "mixed".',
                    required: true
                ))
                ->addProperty(new ToolProperty(
                    name: 'limit',
                    type: PropertyType::INTEGER,
                    description: 'Jumlah maksimal emiten yang akan dikembalikan (default 10).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'min_volume',
                    type: PropertyType::INTEGER,
                    description: 'Volume minimum (lot) untuk menyaring saham yang terlalu sepi, misalnya 100000.',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'date',
                    type: PropertyType::STRING,
                    description: 'Tanggal perdagangan dalam format YYYY-MM-DD. Jika kosong gunakan tanggal terakhir.',
                    required: false
                ))
                ->setCallable(function (string $direction, ?int $limit = 10, ?int $min_volume = null, ?string $date = null): array {
                    $limit = $limit && $limit > 0 ? $limit : 10;

                    $query = TradingInfo::with('stockCompany');

                    if ($date) {
                        $query->whereDate('date', $date);
                    } else {
                        $latestDate = TradingInfo::max('date');
                        if ($latestDate) {
                            $query->whereDate('date', $latestDate);
                        }
                    }

                    if ($min_volume) {
                        $query->where('volume', '>=', $min_volume);
                    }

                    $direction = strtolower($direction);
                    if ($direction === 'bullish') {
                        $query->where('change', '>', 0)->orderByDesc('change');
                    } elseif ($direction === 'bearish') {
                        $query->where('change', '<', 0)->orderBy('change');
                    } else {
                        $query->orderByDesc('value');
                    }

                    $rows = $query->limit($limit)->get();

                    return $rows->map(function (TradingInfo $row) {
                        /** @var StockCompany|null $company */
                        $company = $row->stockCompany;

                        return [
                            'kode_emiten' => $row->kode_emiten,
                            'company_name' => $company?->nama_emiten,
                            'sector' => $company?->sektor,
                            'industry' => $company?->industri,
                            'date' => optional($row->date)->format('Y-m-d'),
                            'previous' => (float) $row->previous,
                            'open' => (float) $row->open_price,
                            'high' => (float) $row->high,
                            'low' => (float) $row->low,
                            'close' => (float) $row->close,
                            'change' => (float) $row->change,
                            'volume' => (float) $row->volume,
                            'value' => (float) $row->value,
                            'foreign_buy' => (float) $row->foreign_buy,
                            'foreign_sell' => (float) $row->foreign_sell,
                        ];
                    })->all();
                }),
        ];
    }

    /**
     * Helper to run a one-shot chat from application code.
     */
    public static function chatOnce(string $userMessage): string
    {
        $state = static::make()
            ->chat(new UserMessage($userMessage))
            ->getMessage();

        return $state->getContent();
    }
}

