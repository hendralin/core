<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tanda Terima BPKB</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .page-container {
            width: 100%;
            height: auto;
            display: table;
            page-break-inside: avoid;
        }

        .tanda-terima-wrapper {
            display: table-cell;
            width: 50%;
            min-height: 21cm;
            position: relative;
            padding: 10px 15px;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .tanda-terima-wrapper:first-child {
            border-right: 1px dashed #000;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72pt;
            font-weight: bold;
            color: rgba(200, 200, 200, 0.3);
            z-index: 0;
            letter-spacing: 8px;
        }

        .tanda-terima-content {
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            margin-top: 20px;
        }

        .header h1 {
            font-size: 32pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: 2px;
        }

        .header h1 .oto {
            color: #2874BA;
        }

        .header .subtitle {
            font-size: 10pt;
            letter-spacing: 5px;
            margin-top: -3px;
            font-weight: normal;
            border-bottom: 1.5px solid #000;
            padding-bottom: 3px;
        }

        .title-box {
            border: 1.5px solid #000;
            text-align: center;
            padding: 4px;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 5px;
            margin-bottom: 8px;
        }

        .content {
            margin-top: 5px;
        }


        .rincian-section {
            margin-bottom: 8px;
        }

        .rincian-label {
            font-size: 10pt;
            margin-bottom: 12px;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .label {
            display: table-cell;
            width: 38%;
            font-size: 10pt;
            vertical-align: top;
            padding-right: 8px;
            padding-left: 25px;
        }

        .colon {
            display: table-cell;
            width: 2%;
            vertical-align: top;
        }

        .value {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            /* border-bottom: 1.5px dotted #000; */
            padding-bottom: 1px;
        }

        .keterangan {
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .keterangan-title {
            font-weight: bold;
            font-size: 10.5pt;
            margin-bottom: 5px;
        }

        .keterangan-content {
            font-size: 10pt;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .keterangan-location {
            text-align: right;
            font-size: 10pt;
            margin-top: 20px;
            margin-bottom: 20px;
            margin-right: 25px;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-left {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-title {
            font-size: 10pt;
            margin-bottom: 50px;
        }

        .signature-line {
            border-top: 0px;
            display: inline-block;
            font-size: 10pt;
        }

        .company-logo {
            max-height: 110px;
            max-width: 350px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Tanda Terima Pertama - ORIGINAL -->
        <div class="tanda-terima-wrapper">
            <div class="watermark">ORIGINAL</div>
            <div class="tanda-terima-content">
                @php
                    $company = \App\Models\Company::first();
                    $logoData = null;
                    if ($company && $company->logo) {
                        // $logoPath = public_path('logos/' . $company->logo);
                        $logoPath = public_path('logos/logo.jpg');
                        // Check if file exists and get base64 encoded data
                        if (file_exists($logoPath)) {
                            $imageData = file_get_contents($logoPath);
                            $mimeType = mime_content_type($logoPath);
                            $logoData = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                        }
                    }
                @endphp
                <div class="header">
                    @if($logoData)
                        <img src="{{ $logoData }}" alt="Company Logo" class="company-logo" />
                    @else
                        <h1>W<span class="oto">OTO</span></h1>
                        <div class="subtitle">WAHANA.OTO</div>
                    @endif
                </div>

                <div class="title-box">TANDA TERIMA BPKB</div>

                <div class="content">
                    <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
                        <div class="label">Jumlah</div>
                        <div class="colon">:</div>
                        <div class="value">1 (satu) Buah BPKB</div>
                    </div>

                    <div class="rincian-section">
                        <div class="rincian-label">Dengan rincian sbb :</div>
                    </div>

                    <div class="row">
                        <div class="label">BPKB No.</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->vehicle->bpkb_number }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Tahun / NOPOL</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->vehicle->year }} / {{ $certificateReceipt->vehicle->police_number }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Merk / Type</div>
                        <div class="colon">:</div>
                        <div class="value">{{ strtoupper($certificateReceipt->vehicle->brand?->name ?? '-') }} {{ strtoupper($certificateReceipt->vehicle->type?->name ?? '-') }}</div>
                    </div>

                    <div class="row">
                        <div class="label">BPKB A/N</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->in_the_name_of }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Faktur asli A/N</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->original_invoice_name }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Fotocopy KTP A/N</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->photocopy_id_card_name }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Blanko Kwitansi</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->receipt_form }}</div>
                    </div>

                    <div class="row">
                        <div class="label">NIK</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->nik }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Form A</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->form_a }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Surat Pelepasan Hak</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->release_of_title_letter }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Dll</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->others }}</div>
                    </div>

                    <div class="keterangan">
                        <div class="keterangan-title">KETERANGAN :</div>
                        <div class="keterangan-content">
                            - Surat surat lengkap telah diperiksa dan diterima dengan baik oleh yang menerima
                        </div>
                        <div class="keterangan-location">
                            Palembang, {{ $certificateReceipt->receipt_date ? \Carbon\Carbon::parse($certificateReceipt->receipt_date)->translatedFormat('j F Y') : '-' }}
                        </div>
                    </div>

                    <div class="signature-section">
                        <div class="signature-left">
                            <div class="signature-title">Yang menyerahkan,</div>
                            <div class="signature-line">{{ $certificateReceipt->transferee }}</div>
                        </div>

                        <div class="signature-right">
                            <div class="signature-title">Yang menerima,</div>
                            <div class="signature-line">{{ $certificateReceipt->receiving_party }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tanda Terima Kedua - COPY -->
        <div class="tanda-terima-wrapper">
            <div class="watermark">COPY</div>
            <div class="tanda-terima-content">
                <div class="header">
                    @if($logoData)
                        <img src="{{ $logoData }}" alt="Company Logo" class="company-logo" />
                    @else
                        <h1>W<span class="oto">OTO</span></h1>
                        <div class="subtitle">WAHANA.OTO</div>
                    @endif
                </div>

                <div class="title-box">TANDA TERIMA BPKB</div>

                <div class="content">
                    <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
                        <div class="label">Jumlah</div>
                        <div class="colon">:</div>
                        <div class="value">1 (satu) Buah BPKB</div>
                    </div>

                    <div class="rincian-section">
                        <div class="rincian-label">Dengan rincian sbb :</div>
                    </div>

                    <div class="row">
                        <div class="label">BPKB No.</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->vehicle->bpkb_number }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Tahun / NOPOL</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->vehicle->year }} / {{ $certificateReceipt->vehicle->police_number }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Merk / Type</div>
                        <div class="colon">:</div>
                        <div class="value">{{ strtoupper($certificateReceipt->vehicle->brand?->name ?? '-') }} {{ strtoupper($certificateReceipt->vehicle->type?->name ?? '-') }}</div>
                    </div>

                    <div class="row">
                        <div class="label">BPKB A/N</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->in_the_name_of }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Faktur asli A/N</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->original_invoice_name }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Fotocopy KTP A/N</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->photocopy_id_card_name }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Blanko Kwitansi</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->receipt_form }}</div>
                    </div>

                    <div class="row">
                        <div class="label">NIK</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->nik }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Form A</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->form_a }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Surat Pelepasan Hak</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->release_of_title_letter }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Dll</div>
                        <div class="colon">:</div>
                        <div class="value">{{ $certificateReceipt->others }}</div>
                    </div>

                    <div class="keterangan">
                        <div class="keterangan-title">KETERANGAN :</div>
                        <div class="keterangan-content">
                            - Surat surat lengkap telah diperiksa dan diterima dengan baik oleh yang menerima
                        </div>
                        <div class="keterangan-location">
                            Palembang, {{ $certificateReceipt->receipt_date ? \Carbon\Carbon::parse($certificateReceipt->receipt_date)->translatedFormat('j F Y') : '-' }}
                        </div>
                    </div>

                    <div class="signature-section">
                        <div class="signature-left">
                            <div class="signature-title">Yang menyerahkan,</div>
                            <div class="signature-line">{{ $certificateReceipt->transferee }}</div>
                        </div>

                        <div class="signature-right">
                            <div class="signature-title">Yang menerima,</div>
                            <div class="signature-line">{{ $certificateReceipt->receiving_party }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
