---
name: rancang-neuronai-untuk-bandar-saham
overview: Merancang integrasi NeuronAI ke dalam aplikasi Laravel "Bandar Saham" untuk membangun agent screening saham (bullish/bearish) dan agent analis SQL berbasis data stock summary & rasio keuangan, dengan UX kombinasi chat + filter yang sudah ada.
todos:
  - id: setup-neuron-core
    content: Evaluasi dan setup NeuronAI di Laravel (instalasi, config provider, kelas BaseStockAgent)
    status: completed
  - id: design-screening-agent
    content: Rancang dan definisikan ScreeningAgent beserta tools untuk menjalankan screening dan menjelaskan hasil
    status: completed
  - id: design-sql-analyst-agent
    content: Rancang SqlAnalystAgent dengan pola query spec aman dan tool eksekusinya
    status: completed
  - id: integrate-livewire-ui
    content: Integrasikan agent dengan komponen Livewire/FluxUI untuk chat + form filter
    status: completed
  - id: data-schema-and-rag
    content: Dokumentasikan schema DB saham/rasio dan siapkan RAG untuk teks profil perusahaan bila diperlukan
    status: completed
  - id: guardrails-monitoring
    content: Tambahkan pembatasan akses, validasi query, logging, dan monitoring Neuron/Inspector.dev
    status: completed
  - id: explore-advanced-features
    content: Desain fitur lanjutan seperti watchlist agent, backtesting assistant, dan scenario planner
    status: completed
isProject: false
---

## Integrasi NeuronAI untuk "Bandar Saham"

### 1. Arsitektur Tingkat Tinggi

- **Core idea**: Tambah lapisan "AI layer" di atas Laravel + Livewire yang berinteraksi dengan:
  - **Database saham** (stock summary, rasio finansial, profil emiten)
  - **Screening engine** (query builder / fungsi existing untuk filter teknikal & fundamental)
  - **UI Livewire/FluxUI** (chat panel + tombol "minta rekomendasi/analisa")
- **Neuron peran utama**:
  - `ScreeningAgent`: agent yang menerima konteks filter + data agregat, lalu menjelaskan, menyarankan tuning filter, dan mengkategorikan bullish/bearish/sidelines.
  - `SqlAnalystAgent`: agent yang bisa men-generate dan/atau mengeksekusi query terhadap data summary & rasio, kemudian merangkum insight.
  - Di kemudian hari, keduanya bisa diorkestrasi di **Workflow** (misal: langkah 1 screening → langkah 2 analisa fundamental lebih dalam).

```mermaid
flowchart TD
  userUI[UserBandarSahamUI] --> livewire[LivewireComponents]
  livewire --> aiLayer[AiLayerControllers]
  aiLayer --> screeningAgent[ScreeningAgent]
  aiLayer --> sqlAgent[SqlAnalystAgent]

  screeningAgent --> neuronCore[NeuronCore]
  sqlAgent --> neuronCore

  neuronCore --> db[StockDB & FinancialDB]
  neuronCore --> ragStore[VectorStore (opsional)]
```



### 2. Pemilihan & Abstraksi Provider Model

- **Langkah**:
  - Bungkus konfigurasi provider melalui environment (`NEURON_PROVIDER`, `OPENAI_API_KEY`, dsb.) sehingga bisa ganti vendor tanpa ubah kode agent.
  - Mulai dengan **satu provider yang paling mudah diakses** (mis. OpenAI atau Gemini), lalu siapkan interface untuk berpindah ke Ollama/local bila perlu.
- **Implementasi**:
  - Buat kelas dasar `BaseStockAgent` yang meng-extend `NeuronAI\Agent\Agent` dan mendefinisikan method `provider()` berdasarkan konfigurasi.
  - Turunannya: `ScreeningAgent`, `SqlAnalystAgent` hanya override `instructions()` dan `tools()`.

### 3. Desain `ScreeningAgent`

- **Tujuan**: Membantu user melakukan screening bullish/bearish berdasarkan filter teknikal/fundamental yang sudah ada di aplikasi.
- **Input ke agent**:
  - Parameter filter user (timeframe, sektor, range rasio, indikator teknikal, dsb.).
  - Hasil screening kasar dari DB (mis. top N kandidat + beberapa metrik utama).
- **Tools penting**:
  - `RunStockScreeningTool`: mengeksekusi ulang filter screening (mengambil data dari DB) bila agent memodifikasi kriteria.
  - `ExplainScreeningResultTool`: bukan tool teknis, tapi pola di mana agent diberi struktur data ringkas (JSON/array) dan diinstruksikan untuk mengembalikan penjelasan natural language + klasifikasi (bullish/bearish/netral).
- **Peran Neuron**:
  - Agent tidak menggantikan logika screening utama (itu tetap SQL/eloquent), tetapi mengelaborasi dan menyarankan.

### 4. Desain `SqlAnalystAgent`

- **Tujuan**: Menjawab pertanyaan seperti:
  - "Bandingkan PER dan PBV emiten sektor perbankan mid-cap 3 tahun terakhir"
  - "Emiten mana yang punya ROE stabil dan DER rendah di sektor consumer?"
- **Pendekatan aman**:
  - **Pattern 1 (disarankan awal)**: Agent hanya membentuk **query specs terstruktur** (mis. nama tabel, kolom, filter, agregasi) → backend Laravel memetakan ke Eloquent/Query Builder → hasil dikembalikan ke agent untuk dijelaskan.
  - **Pattern 2 (lanjutan)**: Agent benar-benar menulis SQL, tapi dibatasi ke views read-only dengan whitelist tabel/kolom.
- **Tools**:
  - `BuildQuerySpecTool`: menghasilkan struktur query (select, where, group_by, order_by, limit) sebagai array.
  - `ExecuteQuerySpecTool`: Laravel yang mengeksekusi query spec tersebut dan mengembalikan sampel hasil ke agent.

### 5. Integrasi Data (DB & RAG)

- **Data existing**: stock summary, rasio finansial, profil perusahaan.
- **Langkah**:
  - Definisikan **schema description** singkat (nama tabel, kolom penting, definisi singkat) yang selalu dikirim ke agent via system prompt atau tool `GetSchemaTool`.
  - Untuk teks panjang (profil perusahaan, MD&A, news singkat), pertimbangkan **Neuron RAG**:
    - Gunakan Data Loader untuk mengambil teks profil.
    - Buat embeddings dan Vector Store per emiten.
    - Buat toolkit RAG (`CompanyProfileRagToolkit`) yang bisa dipanggil agent untuk menarik insight naratif.

### 6. Lapisan Laravel + Livewire + FluxUI

- **UI pattern** (sesuai preferensi "kombinasi chat + form"):
  - Halaman screening sekarang tetap punya **form filter biasa**.
  - Tambah panel **chat/assistant** di sisi kanan (komponen Livewire) yang terhubung ke `ScreeningAgent`.
    - User bisa klik "Jelaskan hasil screening" atau "Optimalkan filter".
  - Untuk halaman analisa fundamental, sediakan input natural language (chat) ke `SqlAnalystAgent` + tombol untuk men-generate query & insight.
- **Backend routing**:
  - Controller/Livewire method memanggil agent:
    - Ambil state filter/parameter.
    - Bangun `UserMessage` + context messages.
    - Panggil `Agent::make()->chat(...)` (atau streaming bila ingin live tokens).

### 7. Keamanan & Guardrail

- **Pembatasan**:
  - Agent hanya boleh mengakses tools yang sudah disediakan: tidak ada arbitrary SQL, tidak ada akses tabel sensitif.
  - Terapkan **whitelist kolom & tabel** di lapisan Laravel sebelum eksekusi query.
  - Rate limiting untuk endpoint AI agar biaya & performa terkontrol.
- **Validasi**:
  - Semua output tool `BuildQuerySpecTool` divalidasi (mis. cek nama kolom/tabel terhadap schema map) sebelum dieksekusi.

### 8. Observability & Evaluasi

- **Monitoring**:
  - Aktifkan integrasi monitoring Neuron (Inspector.dev) bila memungkinkan, untuk melacak:
    - Tool calls
    - Lama eksekusi
    - Error provider/API
  - Log tiap keputusan agent penting (mis. perubahan filter besar) dengan ID user.
- **Eval**:
  - Buat set skenario QA sederhana: mis. prompt standar yang harus menghasilkan jawaban/struktur tertentu (top-K emiten, klasifikasi bullish/bearish yang wajar) dan jalankan secara berkala.

### 9. Roadmap Pengembangan Fitur Tambahan

- **Fitur lanjutan yang bisa dikembangkan**:
  - **Watchlist Agent**: agent yang memantau watchlist user dan mengirim summary harian (berita, perubahan rasio, sinyal teknikal) dengan bahasa natural.
  - **Backtesting Assistant**: user menjelaskan strategi sederhana, agent menerjemahkan menjadi parameter backtest + mengeksekusi di engine backtest yang sudah ada.
  - **Explainer untuk indikator teknikal**: agent menjelaskan kenapa satu emiten diberi label bullish/bearish dengan menyebut metrik konkret.
  - **Scenario planner**: "Bagaimana kalau suku bunga naik X%?" → agent mengambil daftar emiten interest-sensitive dan memberi analisa naratif.

### 10. Struktur Kode Tingkat Tinggi (Contoh)

- Namespace Laravel yang rapi, misal:
  - `App\Neuron\BaseStockAgent`
  - `App\Neuron\Agents\ScreeningAgent`
  - `App\Neuron\Agents\SqlAnalystAgent`
  - `App\Neuron\Toolkits\StockScreeningToolkit`
  - `App\Neuron\Toolkits\SqlQueryToolkit`
- Livewire:
  - `App\Livewire\Screening\ScreeningPage`
  - `App\Livewire\Screening\ScreeningAssistantPanel`
  - `App\Livewire\Analysis\SqlAnalystPanel`

Plan ini fokus menambahkan Neuron sebagai lapisan AI terkontrol di atas screening & analisa yang sudah ada, tanpa mengganti logika bisnis inti di Laravel/SQL, sehingga bisa dikembangkan bertahap dan aman untuk produksi.