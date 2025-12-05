<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Serah Terima Kendaraan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
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

        @page {
            margin: 0;
            size: A4 landscape;
        }

        .container {
            width: 100%;
            height: auto;
            display: table;
            page-break-inside: avoid;
        }

        .kwitansi {
            display: table-cell;
            width: 50%;
            min-height: 21cm;
            position: relative;
            padding: 10px 15px;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .kwitansi:first-child {
            border-right: 1px dashed #000;
        }

        .kwitansi:last-child {
            border-right: none;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            margin-top: 20px;
        }

        .logo {
            font-size: 32pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .logo .w {
            color: #333;
        }

        .logo .oto {
            color: #0066cc;
        }

        .tagline {
            font-size: 10pt;
            letter-spacing: 4px;
            color: #333;
            border-bottom: 1px solid #333;
            padding-bottom: 2px;
        }

        .title-box {
            border: 2px solid #000;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 15px;
        }

        .content {
            font-size: 10pt;
            line-height: 1.6;
            page-break-inside: avoid;
        }

        .field {
            margin-bottom: 5px;
        }

        .field-inline {
            display: inline-block;
        }

        .label {
            display: inline-block;
            width: 80px;
        }

        .dots {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 200px;
        }

        .kepada-section {
            margin: 1px 0;
        }

        .specification {
            margin: 15px 0;
        }

        .box-field {
            border: 1px solid #000;
            padding: 7px;
            min-height: 20px;
            margin-bottom: 1px;
        }

        .accessories {
            margin: 10px 0;
            font-size: 9pt;
        }

        .accessories-item {
            display: inline-block;
            margin-right: 15px;
        }

        .perhatian {
            margin: 5px 0;
            font-size: 9pt;
            line-height: 1.5;
        }

        .signature-section {
            margin-top: 15px;
        }

        .signature-row {
            display: table;
            width: 100%;
        }

        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-title {
            font-size: 10pt;
            margin-bottom: 40px;
        }

        .signature-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 150px;
        }

        .city-date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72pt;
            color: rgba(200, 200, 200, 0.3);
            z-index: -1;
            pointer-events: none;
            font-weight: bold;
        }

        .kwitansi {
            position: relative;
        }

        .company-logo {
            max-height: 110px;
            max-width: 350px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ORIGINAL -->
        <div class="kwitansi">
            <div class="watermark">ORIGINAL</div>
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

            <div class="title-box">
                BERITA ACARA SERAH TERIMA KENDARAAN
            </div>

            <div class="content">
                <div class="field">
                    @if($handover && $handover->handover_date)
                        @php
                            $date = \Carbon\Carbon::parse($handover->handover_date);
                            $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                            $months = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
                            $dayName = $days[$date->format('l')] ?? $date->format('l');
                            $monthName = $months[$date->format('F')] ?? $date->format('F');
                        @endphp
                        Pada hari ini {{ $dayName }}, Tanggal {{ $date->format('j') }} {{ $monthName }} {{ $date->format('Y') }}
                    @else
                        Pada hari ini _____________, Tanggal _____________
                    @endif
                    telah dilakukan serah terima dari.
                </div>

                <div class="field">
                    <span class="label" style="margin-left: 40px;">Nama</span> :
                    {{ $handover->handover_from }}
                </div>

                <div class="field">
                    <span class="label" style="margin-left: 40px;">Alamat</span> :
                    {{ $handover->handover_from_address }}
                </div>

                <div class="kepada-section">
                    <div style="margin-bottom: 8px;">Kepada</div>
                    <div class="field" style="margin-left: 40px;">
                        <span class="label">Nama</span> :
                        {{ $handover->handover_to }}
                    </div>
                    <div class="field" style="margin-left: 40px;">
                        <span class="label">Alamat</span> :
                        {{ $handover->handover_to_address }}
                    </div>
                </div>

                <div class="specification">
                    atas 1 (Satu) unit kendaraan bermotor dengan spesifikasi sebagai berikut :
                </div>

                <div class="field">
                    Merk / Type : {{ $handover->vehicle->brand->name }} {{ $handover->vehicle->type->name }}
                    Tahun : {{ $handover->vehicle->year }}
                    Nopol : {{ $handover->vehicle->police_number }}
                </div>

                <div class="box-field">
                    Nomor Rangka : <span style="font-weight: bold; font-size: 11pt;">{{ $handover->vehicle->chassis_number }}</span>
                </div>

                <div class="box-field">
                    Nomor Mesin : <span style="font-weight: bold; font-size: 11pt;">{{ $handover->vehicle->engine_number }}</span>
                </div>

                <div class="accessories">
                    Berikut segala kelengkapan kendaraan yang berupa :<br>
                    <div style="margin-top: 5px;">
                        <span class="accessories-item">STNK Asli &nbsp;&nbsp;&nbsp; ({{ $handover->vehicle->file_stnk ? 'Ada' : 'Tidak' }});</span>
                        <span class="accessories-item">Ban Serep &nbsp;&nbsp;&nbsp; ({{ $handover->vehicle->vehicleEquipment->ban_serep ? 'Ada' : 'Tidak' }});</span>
                        <span class="accessories-item">Dongkrak &nbsp;&nbsp;&nbsp; ({{ $handover->vehicle->vehicleEquipment->dongkrak ? 'Ada' : 'Tidak' }});</span><br>
                        <span class="accessories-item">Kunci Roda &nbsp;&nbsp; ({{ $handover->vehicle->vehicleEquipment->kunci_roda ? 'Ada' : 'Tidak' }});</span>
                        <span class="accessories-item">Kunci Serep &nbsp;&nbsp; ({{ $handover->vehicle->vehicleEquipment->kunci_serep ? 'Ada' : 'Tidak' }});</span>
                    </div>
                </div>

                <div class="perhatian">
                    <strong>PERHATIAN:</strong> Semua telah diterima dalam kondisi Baik / Tanpa Syarat.<br>
                    Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya.
                </div>

                <div class="city-date">
                    Palembang, {{ \Carbon\Carbon::parse($handover->handover_date)->format('d F Y') }}
                </div>

                <div class="signature-section">
                    <div class="signature-row">
                        <div class="signature-col">
                            <div class="signature-title">Yang menyerahkan,</div>
                            <div>( {{ $handover->transferee }} )</div>
                        </div>
                        <div class="signature-col">
                            <div class="signature-title">Yang menerima,</div>
                            <div>( {{ $handover->receiving_party }} )</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COPY -->
        <div class="kwitansi">
            <div class="watermark">COPY</div>
            <div class="header">
                @if($logoData)
                    <img src="{{ $logoData }}" alt="Company Logo" class="company-logo" />
                @else
                    <h1>W<span class="oto">OTO</span></h1>
                    <div class="subtitle">WAHANA.OTO</div>
                @endif
            </div>

            <div class="title-box">
                BERITA ACARA SERAH TERIMA KENDARAAN
            </div>

            <div class="content">
                <div class="field">
                    @if($handover && $handover->handover_date)
                        @php
                            $date = \Carbon\Carbon::parse($handover->handover_date);
                            $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                            $months = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
                            $dayName = $days[$date->format('l')] ?? $date->format('l');
                            $monthName = $months[$date->format('F')] ?? $date->format('F');
                        @endphp
                        Pada hari ini {{ $dayName }}, Tanggal {{ $date->format('j') }} {{ $monthName }} {{ $date->format('Y') }}
                    @else
                        Pada hari ini _____________, Tanggal _____________
                    @endif
                    telah dilakukan serah terima dari.
                </div>

                <div class="field">
                    <span class="label" style="margin-left: 40px;">Nama</span> :
                    {{ $handover->handover_from }}
                </div>

                <div class="field">
                    <span class="label" style="margin-left: 40px;">Alamat</span> :
                    {{ $handover->handover_from_address }}
                </div>

                <div class="kepada-section">
                    <div style="margin-bottom: 8px;">Kepada</div>
                    <div class="field" style="margin-left: 40px;">
                        <span class="label">Nama</span> :
                        {{ $handover->handover_to }}
                    </div>
                    <div class="field" style="margin-left: 40px;">
                        <span class="label">Alamat</span> :
                        {{ $handover->handover_to_address }}
                    </div>
                </div>

                <div class="specification">
                    atas 1 (Satu) unit kendaraan bermotor dengan spesifikasi sebagai berikut :
                </div>

                <div class="field">
                    Merk / Type : {{ $handover->vehicle->brand->name }} {{ $handover->vehicle->type->name }}
                    Tahun : {{ $handover->vehicle->year }}
                    Nopol : {{ $handover->vehicle->police_number }}
                </div>

                <div class="box-field">
                    Nomor Rangka : <span style="font-weight: bold; font-size: 11pt;">{{ $handover->vehicle->chassis_number }}</span>
                </div>

                <div class="box-field">
                    Nomor Mesin : <span style="font-weight: bold; font-size: 11pt;">{{ $handover->vehicle->engine_number }}</span>
                </div>

                <div class="accessories">
                    Berikut segala kelengkapan kendaraan yang berupa :<br>
                    <div style="margin-top: 5px;">
                        <span class="accessories-item">STNK Asli &nbsp;&nbsp;&nbsp; ({{ $handover->vehicle->file_stnk ? 'Ada' : 'Tidak' }});</span>
                        <span class="accessories-item">Ban Serep &nbsp;&nbsp;&nbsp; ({{ $handover->vehicle->vehicleEquipment->ban_serep ? 'Ada' : 'Tidak' }});</span>
                        <span class="accessories-item">Dongkrak &nbsp;&nbsp;&nbsp; ({{ $handover->vehicle->vehicleEquipment->dongkrak ? 'Ada' : 'Tidak' }});</span><br>
                        <span class="accessories-item">Kunci Roda &nbsp;&nbsp; ({{ $handover->vehicle->vehicleEquipment->kunci_roda ? 'Ada' : 'Tidak' }});</span>
                        <span class="accessories-item">Kunci Serep &nbsp;&nbsp; ({{ $handover->vehicle->vehicleEquipment->kunci_serep ? 'Ada' : 'Tidak' }});</span>
                    </div>
                </div>

                <div class="perhatian">
                    <strong>PERHATIAN:</strong> Semua telah diterima dalam kondisi Baik / Tanpa Syarat.<br>
                    Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya.
                </div>

                <div class="city-date">
                    Palembang, {{ \Carbon\Carbon::parse($handover->handover_date)->format('d F Y') }}
                </div>

                <div class="signature-section">
                    <div class="signature-row">
                        <div class="signature-col">
                            <div class="signature-title">Yang menyerahkan,</div>
                            <div>( {{ $handover->transferee }} )</div>
                        </div>
                        <div class="signature-col">
                            <div class="signature-title">Yang menerima,</div>
                            <div>( {{ $handover->receiving_party }} )</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
