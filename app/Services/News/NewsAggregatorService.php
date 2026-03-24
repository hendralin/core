<?php

declare(strict_types=1);

namespace App\Services\News;

use App\Models\News;
use App\Models\StockCompany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class NewsAggregatorService
{
    private const IDX_BERITA_URL = 'https://idx.co.id/id/berita/berita';

    private const IDX_BASE = 'https://idx.co.id';

    /** @var list<string> */
    private const NEGATIVE_KEYWORDS = [
        'rugi', 'gugatan', 'suspensi', 'denda', 'gagal bayar', 'wanprestasi', 'default',
        'penundaan', 'delisting', 'sanksi', 'pelanggaran', 'investigasi', 'kecurangan',
        'negative', 'loss', 'lawsuit', 'fine',
    ];

    /**
     * @return array{code: string, items: list<array<string, mixed>>, meta: array<string, mixed>}
     */
    public function headlines(
        string $code,
        int $limit = 10,
        int $days = 30,
        bool $includeLive = true,
        bool $negativeOnly = false,
    ): array {
        $code = strtoupper(trim($code));
        $limit = max(1, min($limit, 20));
        $days = max(1, min($days, 365));

        $companyName = StockCompany::query()
            ->where('kode_emiten', $code)
            ->value('nama_emiten');

        $sourcesUsed = [];
        $items = [];

        $internal = $this->fetchFromIdxNewsTable($code, $companyName, $days, $limit * 2);
        if ($internal !== []) {
            $sourcesUsed[] = 'internal_idx_news';
            $items = array_merge($items, $internal);
        }

        if ($includeLive) {
            $snips = $this->fetchFromStockbitSnipsApi($code, $companyName, $limit * 2);
            if ($snips !== []) {
                $sourcesUsed[] = 'stockbit_snips';
                $items = array_merge($items, $snips);
            }

            $rss = $this->fetchFromGoogleNewsRss($code, $companyName, $limit * 2);
            if ($rss !== []) {
                $sourcesUsed[] = 'google_news_rss';
                $items = array_merge($items, $rss);
            }
        }

        $items = $this->dedupeByUrl($items);
        usort($items, static function (array $a, array $b): int {
            $ta = $a['published_at'] ?? '';
            $tb = $b['published_at'] ?? '';

            return strcmp((string) $tb, (string) $ta);
        });

        foreach ($items as &$item) {
            $item['sentiment_hint'] = $this->sentimentHint((string) ($item['headline'] ?? ''), (string) ($item['summary'] ?? ''));
        }
        unset($item);

        if ($negativeOnly) {
            $items = array_values(array_filter(
                $items,
                static fn (array $row): bool => $row['sentiment_hint'] === 'negative'
            ));
        }

        $items = array_slice($items, 0, $limit);

        return [
            'code' => $code,
            'items' => $items,
            'meta' => [
                'total' => count($items),
                'sources_used' => $sourcesUsed,
                'fetched_at' => now()->toIso8601String(),
                'company_name' => $companyName,
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchFromIdxNewsTable(string $code, ?string $companyName, int $days, int $maxRows): array
    {
        $since = Carbon::now()->subDays($days)->startOfDay();

        $query = News::query()
            ->where('published_date', '>=', $since)
            ->orderByDesc('published_date');

        $query->where(function ($q) use ($code, $companyName): void {
            $q->where('title', 'like', '%' . $code . '%')
                ->orWhere('summary', 'like', '%' . $code . '%')
                ->orWhere('tags', 'like', '%' . $code . '%')
                ->orWhere('contents', 'like', '%' . $code . '%');

            if ($companyName !== null && $companyName !== '') {
                $q->orWhere('title', 'like', '%' . $companyName . '%')
                    ->orWhere('summary', 'like', '%' . $companyName . '%')
                    ->orWhere('contents', 'like', '%' . $companyName . '%');
            }
        });

        $rows = $query->limit($maxRows)->get();
        $out = [];

        foreach ($rows as $news) {
            $url = $this->buildIdxArticleUrl($news->path_base, $news->path_file);
            $out[] = [
                'headline' => (string) $news->title,
                'url' => $url,
                'source' => 'idx_news_db',
                'published_at' => $news->published_date?->toIso8601String(),
                'matched_code' => $code,
                'summary' => $news->summary !== null ? Str::limit(strip_tags((string) $news->summary), 300) : null,
            ];
        }

        return $out;
    }

    private function buildIdxArticleUrl(?string $pathBase, ?string $pathFile): string
    {
        if ($pathBase !== null && $pathFile !== null && $pathBase !== '' && $pathFile !== '') {
            return self::IDX_BASE . rtrim($pathBase, '/') . $pathFile;
        }

        return self::IDX_BERITA_URL;
    }

    /**
     * String `q` untuk GeneralSearch: alias dari config (mis. BBCA → bca) atau kode emiten lowercase.
     */
    private function stockbitSnipsSearchQuery(string $code): string
    {
        $aliases = config('news.stockbit_snips.query_aliases', []);
        if (is_array($aliases) && isset($aliases[$code])) {
            return mb_strtolower((string) $aliases[$code]);
        }

        return mb_strtolower($code);
    }

    /**
     * Cocokkan item hasil API dengan emiten: kode, $TICKER, nama, kata kunci `q`, tags, highlight.
     *
     * @param  array<string, mixed>  $row
     */
    private function stockbitSnipsItemMatchesEmiten(
        string $code,
        ?string $companyName,
        string $searchQuery,
        array $row
    ): bool {
        $codeLower = mb_strtolower($code);
        $sq = mb_strtolower($searchQuery);
        $nameLower = $companyName !== null && $companyName !== '' ? mb_strtolower($companyName) : null;

        $title = (string) ($row['title'] ?? '');
        $body = isset($row['body']) ? strip_tags((string) $row['body']) : '';
        $excerpt = (string) ($row['excerpt'] ?? '');

        $tags = $row['tags'] ?? [];
        $tagsStr = '';
        if (is_array($tags)) {
            $tagsStr = implode(' ', array_map(static fn ($t): string => (string) $t, $tags));
        }

        $hlStr = '';
        $highlights = $row['highlight'] ?? [];
        if (is_array($highlights)) {
            foreach ($highlights as $h) {
                $hlStr .= ' ' . strip_tags((string) $h);
            }
        }

        $categories = $row['categories'] ?? [];
        $catStr = '';
        if (is_array($categories)) {
            $catStr = implode(' ', array_map(static fn ($c): string => (string) $c, $categories));
        }

        $blob = mb_strtolower($title . ' ' . $body . ' ' . $excerpt . ' ' . $tagsStr . ' ' . $hlStr . ' ' . $catStr);

        if (str_contains($blob, $codeLower)) {
            return true;
        }

        if (str_contains($blob, '$' . $codeLower)) {
            return true;
        }

        if ($nameLower !== null && str_contains($blob, $nameLower)) {
            return true;
        }

        if ($sq !== '' && str_contains($blob, $sq)) {
            return true;
        }

        return false;
    }

    /**
     * Pencarian Stockbit Snips (GeneralSearch). Perlu STOCKBIT_SNIPS_CRUMB di .env.
     *
     * Respons JSON: status=1, items[] dengan title, itemUrl, publishOn (ms), body, excerpt, tags, highlight, dll.
     *
     * @return list<array<string, mixed>>
     */
    private function fetchFromStockbitSnipsApi(string $code, ?string $companyName, int $maxItems): array
    {
        $crumb = (string) config('news.stockbit_snips.crumb', '');
        if ($crumb === '') {
            return [];
        }

        $base = rtrim((string) config('news.stockbit_snips.base_url', 'https://snips.stockbit.com'), '/');
        $endpoint = $base . '/api/search/GeneralSearch';
        $size = min(10, max(1, $maxItems));
        $searchQuery = $this->stockbitSnipsSearchQuery($code);

        $verifySsl = (bool) config('news.stockbit_snips.verify_ssl', true);

        try {
            $response = Http::timeout(15)
                ->withOptions(['verify' => $verifySsl])
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json',
                    'Referer' => $base . '/',
                ])
                ->get($endpoint, [
                    'crumb' => $crumb,
                    'q' => $searchQuery,
                    'p' => 0,
                    'size' => $size,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $json = $response->json();
            if (! is_array($json) || (int) ($json['status'] ?? 0) !== 1) {
                return [];
            }

            $rawItems = $json['items'] ?? [];
            if (! is_array($rawItems)) {
                return [];
            }

            $out = [];

            foreach ($rawItems as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $title = trim((string) ($row['title'] ?? ''));
                $path = (string) ($row['itemUrl'] ?? '');
                if ($title === '' || $path === '') {
                    continue;
                }

                if (! $this->stockbitSnipsItemMatchesEmiten($code, $companyName, $searchQuery, $row)) {
                    continue;
                }

                $path = str_starts_with($path, 'http') ? $path : $base . $path;
                $body = isset($row['body']) ? strip_tags((string) $row['body']) : '';
                $excerpt = trim((string) ($row['excerpt'] ?? ''));

                $ms = isset($row['publishOn']) ? (int) $row['publishOn'] : 0;
                $publishedAt = null;
                if ($ms > 0) {
                    try {
                        $publishedAt = Carbon::createFromTimestamp((int) floor($ms / 1000))->toIso8601String();
                    } catch (\Throwable) {
                        $publishedAt = null;
                    }
                }

                $summary = $excerpt !== '' ? Str::limit($excerpt, 300) : ($body !== '' ? Str::limit($body, 300) : null);

                $tags = $row['tags'] ?? [];
                $tagsOut = is_array($tags) ? array_values(array_map(static fn ($t): string => (string) $t, $tags)) : [];

                $categories = $row['categories'] ?? [];
                $categoriesOut = is_array($categories) ? array_values(array_map(static fn ($c): string => (string) $c, $categories)) : [];

                $item = [
                    'headline' => $title,
                    'url' => $path,
                    'source' => 'stockbit_snips',
                    'published_at' => $publishedAt,
                    'matched_code' => $code,
                    'summary' => $summary,
                ];

                if (isset($row['id']) && $row['id'] !== '') {
                    $item['stockbit_snips_id'] = (string) $row['id'];
                }

                if (isset($row['imageUrl']) && is_string($row['imageUrl']) && $row['imageUrl'] !== '') {
                    $item['image_url'] = $row['imageUrl'];
                }

                if ($tagsOut !== []) {
                    $item['tags'] = $tagsOut;
                }

                if ($categoriesOut !== []) {
                    $item['categories'] = $categoriesOut;
                }

                $out[] = $item;

                if (count($out) >= $maxItems) {
                    break;
                }
            }

            return $out;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Headline tambahan via Google News RSS (bukan scraping halaman Berita IDX).
     *
     * @return list<array<string, mixed>>
     */
    private function fetchFromGoogleNewsRss(string $code, ?string $companyName, int $maxItems): array
    {
        $query = $code . ' saham Indonesia IDX';
        $url = 'https://news.google.com/rss/search?q=' . rawurlencode($query) . '&hl=id&gl=ID&ceid=ID:id';

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; BandarSahamNewsAggregator/1.0)',
                    'Accept' => 'application/rss+xml, application/xml, text/xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                return [];
            }

            $xml = @simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml === false) {
                return [];
            }

            $items = [];
            $seen = [];
            $codeLower = mb_strtolower($code);
            $nameLower = $companyName !== null && $companyName !== '' ? mb_strtolower($companyName) : null;

            foreach ($xml->channel->item ?? [] as $item) {
                $title = trim((string) $item->title);
                $link = trim((string) $item->link);
                $pub = isset($item->pubDate) ? (string) $item->pubDate : null;
                $description = isset($item->description) ? strip_tags((string) $item->description) : '';

                if ($title === '' || $link === '') {
                    continue;
                }

                $titleLower = mb_strtolower($title);
                $descLower = mb_strtolower($description);
                $blob = $titleLower . ' ' . $descLower;

                $relevant = str_contains($blob, $codeLower)
                    || ($nameLower !== null && str_contains($blob, $nameLower));

                if (! $relevant) {
                    continue;
                }

                $key = md5(mb_strtolower($link));
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;

                $publishedAt = null;
                if ($pub !== null && $pub !== '') {
                    try {
                        $publishedAt = Carbon::parse($pub)->toIso8601String();
                    } catch (\Throwable) {
                        $publishedAt = null;
                    }
                }

                $items[] = [
                    'headline' => $title,
                    'url' => $link,
                    'source' => 'google_news_rss',
                    'published_at' => $publishedAt,
                    'matched_code' => $code,
                    'summary' => isset($item->description) ? Str::limit(strip_tags((string) $item->description), 300) : null,
                ];

                if (count($items) >= $maxItems) {
                    break;
                }
            }

            return $items;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param list<array<string, mixed>> $items
     * @return list<array<string, mixed>>
     */
    private function dedupeByUrl(array $items): array
    {
        $seen = [];
        $out = [];

        foreach ($items as $item) {
            $url = isset($item['url']) ? mb_strtolower((string) $item['url']) : '';
            $key = $url !== '' ? $url : md5((string) ($item['headline'] ?? ''));

            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $item;
        }

        return $out;
    }

    private function sentimentHint(string $headline, string $summary): ?string
    {
        $blob = mb_strtolower($headline . ' ' . $summary);

        foreach (self::NEGATIVE_KEYWORDS as $kw) {
            if (str_contains($blob, mb_strtolower($kw))) {
                return 'negative';
            }
        }

        return 'neutral';
    }
}
