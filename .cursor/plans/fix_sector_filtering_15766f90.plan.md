---
name: Fix sector filtering
overview: Perbaiki SqlAnalystAgent agar filter sektor/industri mengacu ke `stock_companies` (kolom `sektor`/`industri`) dan sediakan tool untuk mengambil daftar sektor `distinct`, sehingga agent tidak lagi mengembalikan “data tidak tersedia” padahal datanya ada.
todos:
  - id: update-sector-industry-filters
    content: Ubah filter `sector`/`industry` di `execute_query_spec` agar memakai `whereHas('stockCompany')` pada kolom `sektor`/`industri`.
    status: completed
  - id: fix-returned-sector-industry
    content: Kembalikan field `sector`/`industry` dari `stockCompany` agar output konsisten dengan `stock_companies`.
    status: completed
  - id: add-distinct-sector-tool
    content: Tambah tool `list_available_sectors` (distinct dari `stock_companies.sektor`), dan perbarui prompt agar dipakai saat user menanyakan sektor.
    status: completed
  - id: verify-with-sample-queries
    content: Verifikasi lewat contoh query sektor bahwa hasil tidak lagi kosong dan daftar sektor distinct muncul.
    status: completed
isProject: false
---

## Tujuan

- Saat user bertanya berdasarkan **sektor**, agent harus mengambil data dengan benar dari `stock_companies.sektor` (distinct) dan tidak gagal karena memfilter kolom yang salah.

## Temuan akar masalah

- Tool `execute_query_spec` di `[app/Neuron/Agents/SqlAnalystAgent.php](app/Neuron/Agents/SqlAnalystAgent.php)` saat ini memfilter sektor lewat `FinancialRatio::where('sector', ...)`, padahal sektor yang kamu referensikan berada di `stock_companies.sektor`.
- Bahkan ketika relasi `stockCompany` sudah di-load, hasil yang dikembalikan masih memakai `$row->sector` (dari `financial_ratios`) bukan `$row->stockCompany?->sektor`.

## Perubahan yang akan dilakukan

- Update `[app/Neuron/Agents/SqlAnalystAgent.php](app/Neuron/Agents/SqlAnalystAgent.php)`:
  - **Filter sektor**: ganti logika menjadi `whereHas('stockCompany', fn($q) => $q->where('sektor', $sector))`.
  - **Filter industri**: ganti menjadi `whereHas('stockCompany', fn($q) => $q->where('industri', $industry))`.
  - **(Opsional fallback aman)**: jika kamu masih ingin dukung data lama, sektor/industri bisa dibuat OR terhadap `financial_ratios.sector/industry` juga.
  - **Return payload**: set `sector`/`industry` dari data `stockCompany` (`sektor`/`industri`) agar konsisten dengan sumber kebenaran.
- Tambahkan tool baru di `SqlAnalystAgent`:
  - `list_available_sectors`: query `StockCompany::query()->select('sektor')->distinct()->orderBy('sektor')->pluck('sektor')` (limit wajar), untuk membantu agent memilih nama sektor yang valid.
  - (Opsional) `list_available_industries` dengan pola sama untuk `industri`.
- Update instruksi SystemPrompt (`toolsUsage`) di `SqlAnalystAgent`:
  - Jika user menyebut sektor tapi tidak spesifik / kemungkinan typo, agent **wajib** memanggil `list_available_sectors` dulu lalu memilih yang paling cocok.

## Verifikasi (setelah implementasi)

- Jalankan 1–2 contoh pertanyaan sektor (mis. “sektor perbankan”): pastikan agent memanggil tool query dan hasilnya tidak kosong.
- Uji tool `list_available_sectors` mengembalikan daftar sektor distinct yang memang ada di `stock_companies.sektor`.

## File yang disentuh

- `[app/Neuron/Agents/SqlAnalystAgent.php](app/Neuron/Agents/SqlAnalystAgent.php)` (utama)
- (Opsional) `[config/stock_schema.php](config/stock_schema.php)` hanya jika perlu menambah deskripsi/kolom terkait agar agent makin jelas.

