---
name: News headlines endpoint
overview: Bangun endpoint publik headline berita emiten (DB internal + live IDX) dan tool agent yang memakainya, sehingga jawaban dapat menampilkan daftar headline beserta URL link.
todos:
  - id: build-news-aggregator-service
    content: Buat NewsAggregatorService untuk gabungkan idx_news internal dan live IDX dengan normalisasi + deduplikasi output.
    status: completed
  - id: add-public-news-endpoint
    content: Tambah NewsController + route GET /api/v1/news/headlines dengan validasi query (code/limit/days/include_live).
    status: completed
  - id: wire-screening-agent-news-tool
    content: Tambah tool get_emiten_news_headlines di ScreeningAgent dan update prompt agar dipakai saat user tanya berita emiten.
    status: completed
  - id: verify-news-headline-flow
    content: Verifikasi endpoint dan respons agent menampilkan headline + URL untuk kode emiten (contoh PADI).
    status: completed
isProject: false
---

# Rancang Tool + Endpoint Berita Emiten

## Sasaran

- Menyediakan endpoint publik yang mengembalikan **headline + URL** untuk emiten tertentu.
- Menambahkan tool di `ScreeningAgent` agar agent bisa menjawab pertanyaan berita negatif/positif dengan data terstruktur (bukan sekadar daftar portal).
- Sumber tahap awal sesuai pilihanmu: **internal DB `idx_news` + live IDX page** ([IDX Berita](https://idx.co.id/id/berita/berita)).

## Desain Arsitektur

- **NewsAggregatorService (baru)**
  - Menggabungkan dua sumber:
    - **Internal**: tabel `idx_news` via model `[app/Models/News.php](app/Models/News.php)`.
    - **Live**: fetch dari halaman IDX Berita (HTTP GET, parse headline + link, fail-safe).
  - Normalisasi output menjadi shape tunggal:
    - `headline`, `url`, `source`, `published_at`, `sentiment_hint` (nullable), `matched_code`.
  - Deduplikasi berbasis URL/slug judul.
- **Public API Endpoint (baru)**
  - `GET /api/v1/news/headlines`
  - Query params: `code` (required), `limit` (default 10, max 20), `days` (default 30), `include_live` (default true).
  - Respons JSON konsisten dengan pola `ApiController::success`.
- **Agent Tool (update `ScreeningAgent`)**
  - Tambah tool `get_emiten_news_headlines` dengan properti `code`, `limit`, `days`, `negative_only`.
  - Tool memanggil service aggregator langsung (bukan HTTP ke endpoint internal) agar lebih stabil dan hemat latency.
  - Prompt diarahkan: untuk pertanyaan berita, wajib panggil tool ini dulu sebelum menyimpulkan.

## Perubahan File yang Direncanakan

- Tambah service aggregator:
  - `[app/Services/News/NewsAggregatorService.php](app/Services/News/NewsAggregatorService.php)`
- Tambah controller API v1:
  - `[app/Http/Controllers/Api/V1/NewsController.php](app/Http/Controllers/Api/V1/NewsController.php)`
- Tambah route publik v1:
  - `[routes/api/v1.php](routes/api/v1.php)`
- Update agent tool + prompt:
  - `[app/Neuron/Agents/ScreeningAgent.php](app/Neuron/Agents/ScreeningAgent.php)`

## Kontrak Endpoint (ringkas)

- Request:
  - `GET /api/v1/news/headlines?code=PADI&limit=10&days=30&include_live=1`
- Response:
  - `data.code`
  - `data.items[]` berisi `headline`, `url`, `source`, `published_at`, `sentiment_hint`
  - `data.meta` berisi `total`, `sources_used`, `fetched_at`

## Validasi & Ketahanan

- `code` dinormalisasi uppercase (`PADI`, `BBCA`).
- Timeout + error isolation untuk sumber live: jika gagal, endpoint tetap return hasil internal.
- Filter sederhana relevansi emiten (match kode pada judul/isi ringkas bila tersedia).
- `negative_only` di tool agent: heuristik keyword negatif ringan (contoh: rugi, gugatan, suspensi, denda, gagal bayar) dengan label `sentiment_hint`, bukan klaim final.

## Verifikasi

- Uji endpoint publik:
  - `code=PADI` mengembalikan item headline + url.
  - `include_live=0` hanya dari DB internal.
  - fallback saat live fetch gagal tetap 200 dengan sumber internal.
- Uji agent:
  - Pertanyaan “apakah ada berita negatif tentang PADI?” memicu tool `get_emiten_news_headlines` dan menampilkan daftar headline + link.

## Catatan Implementasi Sumber

- Sumber referensi berita yang dipakai pada fase ini:
  - [IDX Berita](https://idx.co.id/id/berita/berita)
  - [Stockbit Snips](https://snips.stockbit.com/) (tetap dicantumkan sebagai referensi di jawaban agent, namun fetch live tahap awal difokuskan IDX agar implementasi aman dan stabil)

