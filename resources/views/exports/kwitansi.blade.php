<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
        }

        .page-container {
            width: 100%;
            height: auto;
        }

        .kwitansi-wrapper {
            width: 100%;
            height: 14cm;
            position: relative;
            padding: 15px 25px 15px 15px;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .kwitansi-wrapper:first-child {
            page-break-after: avoid;
            border-bottom: 1px dashed #000;
        }

        .kwitansi-wrapper:last-child {
            page-break-before: avoid;
        }

        .separator {
            width: 100%;
            height: 0;
            border-bottom: 1px dashed #000;
            margin: 0;
            position: absolute;
            bottom: 0;
            left: 0;
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
            letter-spacing: 10px;
        }

        .kwitansi-content {
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 36pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: 2px;
        }

        .header h1 .oto {
            color: #2874BA;
        }

        .header .subtitle {
            font-size: 11pt;
            letter-spacing: 6px;
            margin-top: -3px;
            font-weight: normal;
        }

        .company-logo {
            max-height: 110px;
            max-width: 350px;
            object-fit: contain;
        }

        .advisor-box {
            float: right;
            border: 1.5px solid #000;
            padding: 2px 8px;
            margin-top: -45px;
            margin-right: 30px;
        }

        .advisor-box div {
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
            line-height: 1.2;
        }

        .title-bar {
            background-color: #C0C0C0;
            text-align: center;
            padding: 6px;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 8px;
            margin-right: 30px;
            clear: both;
        }

        .content {
            margin-top: 10px;
            padding: 0 12px;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .label {
            display: table-cell;
            width: 35%;
            font-size: 8.5pt;
            vertical-align: top;
            padding-right: 8px;
        }

        .colon {
            display: table-cell;
            width: 2%;
            vertical-align: top;
        }

        .value {
            display: table-cell;
            width: 120%;
            vertical-align: top;
        }

        .value-box {
            background-color: #D3D3D3;
            padding: 3px 8px;
            min-height: 16px;
            margin-right: 30px;
        }

        .split-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .left-section {
            display: table-cell;
            width: 50%;
            padding-right: 4px;
        }

        .right-section {
            display: table-cell;
            width: 50%;
            padding-left: 4px;
        }

        .inner-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .inner-label {
            display: table-cell;
            width: 45%;
            font-size: 8.5pt;
        }

        .inner-colon {
            display: table-cell;
            width: 5%;
        }

        .inner-value {
            display: table-cell;
            width: 50%;
        }

        .pelunasan {
            font-size: 8.5pt;
            margin-top: 3px;
            margin-bottom: 8px;
        }

        .keterangan {
            margin-top: 6px;
            margin-bottom: 8px;
        }

        .keterangan-table {
            display: table;
            width: 100%;
        }

        .keterangan-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .keterangan-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
            padding-right: 20px;
        }

        .keterangan-title {
            font-weight: bold;
            font-size: 7.5pt;
            margin-bottom: 3px;
        }

        .keterangan-content {
            font-size: 6.5pt;
            line-height: 1.3;
            padding-left: 8px;
        }

        .keterangan-city {
            font-size: 8pt;
            margin-bottom: 3px;
            margin-right: 135px;
        }

        .keterangan-signature {
            margin-top: 80px;
            margin-right: 40px;
            display: inline-block;
            min-width: 100px;
            text-align: center;
            font-size: 7pt;
        }

        .footer {
            display: table;
            width: 100%;
            margin-top: 5px;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
            padding-right: 40px;
        }

        .amount-label {
            display: inline-block;
            vertical-align: middle;
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 3px;
        }

        .amount-box {
            background-color: #D3D3D3;
            padding: 3px 8px;
            display: inline-block;
            vertical-align: middle;
            min-width: 150px;
            font-size: 9pt;
            font-weight: bold;
            margin-left: 2px;
        }

        .terbilang-text {
            font-size: 7pt;
            font-weight: normal;
            margin-top: 2px;
            line-height: 1.2;
            text-align: center;
        }

        .signature-section {
            margin-top: 6px;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px dotted #000;
            display: inline-block;
            min-width: 120px;
            text-align: center;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Kwitansi Pertama - ORIGINAL -->
        <div class="kwitansi-wrapper">
            <div class="watermark">ORIGINAL</div>
            <div class="kwitansi-content">
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

                <div class="advisor-box">
                    <div style="border-bottom: 1px solid #000;">ADV.ANDRI SUSANO,SH</div>
                    <div>PENASIHAT HUKUM</div>
                </div>

                <div class="title-bar">KWITANSI NO. {{ $paymentReceipt->payment_number }}</div>

                <div class="content">
                    <div class="row">
                        <div class="label">SUDAH TERIMA DARI</div>
                        <div class="colon">:</div>
                        <div class="value">&nbsp;&nbsp;&nbsp;{{ strtoupper($paymentReceipt->vehicle->buyer_name) }}</div>
                    </div>

                    <div class="row">
                        <div class="label">BANYAKNYA UANG</div>
                        <div class="colon">:</div>
                        <div class="value">
                            <div class="value-box">&nbsp;{{ strtoupper(terbilang($paymentReceipt->amount)) }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="label"></div>
                        <div class="colon"></div>
                        <div class="value">
                            <div class="value-box"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="label">UNTUK PEMBAYARAN</div>
                        <div class="colon">:</div>
                        <div class="value">&nbsp;&nbsp;&nbsp;{{ $paymentReceipt->description }}</div>
                    </div>

                    <div class="split-row">
                        <div class="left-section">
                            <div class="inner-row">
                                <div class="inner-label">MERK MOBIL</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ strtoupper($paymentReceipt->vehicle->brand?->name ?? '-') }} {{ strtoupper($paymentReceipt->vehicle->type?->name ?? '-') }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">TAHUN PEMBUATAN</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->year }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">WARNA</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ strtoupper($paymentReceipt->vehicle->color) }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">ALAMAT</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ strtoupper($paymentReceipt->vehicle->buyer_address) }}</div>
                            </div>
                        </div>

                        <div class="right-section">
                            <div class="inner-row">
                                <div class="inner-label">NO. POLISI</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->police_number }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">NO. RANGKA</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->chassis_number }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">NO. MESIN</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->engine_number }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">NO. BPKB</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->bpkb_number }}</div>
                            </div>
                        </div>
                    </div>

                    @if($paymentReceipt->remaining_balance > 0)
                    <div class="pelunasan">
                        Sisa Pelunasan Senilai Rp. {{ number_format($paymentReceipt->remaining_balance, 0, ',', '.') }},- akan diselesaikan selambat lambatnya pada tanggal {{ \Carbon\Carbon::parse($paymentReceipt->must_be_settled_date)->format('d/m/Y') }}.
                    </div>
                    @endif

                    <div class="keterangan">
                        <div class="keterangan-table">
                            <div class="keterangan-left">
                                <div class="keterangan-title">KETERANGAN :</div>
                                <div class="keterangan-content">
                                    - Surat surat lengkap telah diperiksa dan diterima dengan baik oleh pembeli<br>
                                    &nbsp;&nbsp;dan kondisi mobil dalam keadaan baik/bekas pakai. Pembayaran dengan<br>
                                    &nbsp;&nbsp;menggunakan cek/bilyet giro dianggap sah apabila cek/bilyet giro sudah<br>
                                    &nbsp;&nbsp;dicairkan atau diterima uangnya dengan baik<br>
                                    - Bila sisa pelunasan tidak diselesaikan pada tanggal yang di sepakati<br>
                                    &nbsp;&nbsp;maka uang panjar dianggap hangus<br>
                                    - Uang panjar dan mobil tidak dapat ditukar atau dikembalikan
                                </div>
                            </div>
                            <div class="keterangan-right">
                                <div class="keterangan-city">Palembang</div>
                                <div class="keterangan-signature">(...................................)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(...................................)</div>
                            </div>
                        </div>
                    </div>

                    <div class="footer">
                        <div class="footer-left">
                            <div class="amount-label">JUMLAH UANG Rp.</div>
                            <div class="amount-box">{{ number_format($paymentReceipt->amount, 0, ',', '.') }},-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kwitansi Kedua - COPY -->
        <div class="kwitansi-wrapper">
            <div class="watermark">COPY</div>
            <div class="kwitansi-content">
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

                <div class="advisor-box">
                    <div style="border-bottom: 1px solid #000;">ADV.ANDRI SUSANO,SH</div>
                    <div>PENASIHAT HUKUM</div>
                </div>

                <div class="title-bar">KWITANSI NO. {{ $paymentReceipt->payment_number }}</div>

                <div class="content">
                    <div class="row">
                        <div class="label">SUDAH TERIMA DARI</div>
                        <div class="colon">:</div>
                        <div class="value">&nbsp;&nbsp;&nbsp;{{ strtoupper($paymentReceipt->vehicle->buyer_name) }}</div>
                    </div>

                    <div class="row">
                        <div class="label">BANYAKNYA UANG</div>
                        <div class="colon">:</div>
                        <div class="value">
                            <div class="value-box">&nbsp;{{ strtoupper(terbilang($paymentReceipt->amount)) }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="label"></div>
                        <div class="colon"></div>
                        <div class="value">
                            <div class="value-box"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="label">UNTUK PEMBAYARAN</div>
                        <div class="colon">:</div>
                        <div class="value">&nbsp;&nbsp;&nbsp;{{ $paymentReceipt->description }}</div>
                    </div>

                    <div class="split-row">
                        <div class="left-section">
                            <div class="inner-row">
                                <div class="inner-label">MERK MOBIL</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ strtoupper($paymentReceipt->vehicle->brand?->name ?? '-') }} {{ strtoupper($paymentReceipt->vehicle->type?->name ?? '-') }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">TAHUN PEMBUATAN</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->year }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">WARNA</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ strtoupper($paymentReceipt->vehicle->color) }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">ALAMAT</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ strtoupper($paymentReceipt->vehicle->buyer_address) }}</div>
                            </div>
                        </div>

                        <div class="right-section">
                            <div class="inner-row">
                                <div class="inner-label">NO. POLISI</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->police_number }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">NO. RANGKA</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->chassis_number }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">NO. MESIN</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->engine_number }}</div>
                            </div>

                            <div class="inner-row">
                                <div class="inner-label">NO. BPKB</div>
                                <div class="inner-colon">:</div>
                                <div class="inner-value">{{ $paymentReceipt->vehicle->bpkb_number }}</div>
                            </div>
                        </div>
                    </div>

                    @if($paymentReceipt->remaining_balance > 0)
                    <div class="pelunasan">
                        Sisa Pelunasan Senilai Rp. {{ number_format($paymentReceipt->remaining_balance, 0, ',', '.') }},- akan diselesaikan selambat lambatnya pada tanggal {{ \Carbon\Carbon::parse($paymentReceipt->must_be_settled_date)->format('d/m/Y') }}.
                    </div>
                    @endif

                    <div class="keterangan">
                        <div class="keterangan-table">
                            <div class="keterangan-left">
                                <div class="keterangan-title">KETERANGAN :</div>
                                <div class="keterangan-content">
                                    - Surat surat lengkap telah diperiksa dan diterima dengan baik oleh pembeli<br>
                                    &nbsp;&nbsp;dan kondisi mobil dalam keadaan baik/bekas pakai. Pembayaran dengan<br>
                                    &nbsp;&nbsp;menggunakan cek/bilyet giro dianggap sah apabila cek/bilyet giro sudah<br>
                                    &nbsp;&nbsp;dicairkan atau diterima uangnya dengan baik<br>
                                    - Bila sisa pelunasan tidak diselesaikan pada tanggal yang di sepakati<br>
                                    &nbsp;&nbsp;maka uang panjar dianggap hangus<br>
                                    - Uang panjar dan mobil tidak dapat ditukar atau dikembalikan
                                </div>
                            </div>
                            <div class="keterangan-right">
                                <div class="keterangan-city">Palembang</div>
                                <div class="keterangan-signature">(...................................)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(...................................)</div>
                            </div>
                        </div>
                    </div>

                    <div class="footer">
                        <div class="footer-left">
                            <div class="amount-label">JUMLAH UANG Rp.</div>
                            <div class="amount-box">{{ number_format($paymentReceipt->amount, 0, ',', '.') }},-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
