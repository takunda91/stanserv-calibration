<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calibration Certificate - {{ $calibration->calibration_number }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            color: #000;
            background: #fff;
        }

        /* ── Security border strips (repeat every page) ── */
        .border-h {
            position: fixed;
            left: 4mm;
            right: 4mm;
            height: 12px;
            background: transparent;
            font-size: 6pt;
            font-weight: bold;
            color: #000;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            letter-spacing: 1.5px;
            line-height: 12px;
            z-index: 300;
            font-family: Arial, sans-serif;
        }

        .border-top {
            top: 4mm;
        }

        .border-bottom {
            bottom: 4mm;
        }

        .border-v {
            position: fixed;
            top: 4mm;
            bottom: 4mm;
            width: 12px;
            background: transparent;
            font-size: 6pt;
            font-weight: bold;
            color: #000;
            white-space: nowrap;
            overflow: hidden;
            writing-mode: vertical-rl;
            z-index: 300;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 1.5px;
            font-family: Arial, sans-serif;
        }

        .border-left {
            left: 4mm;
            transform: rotate(180deg);
        }

        .border-right {
            right: 4mm;
        }

        /* ── Watermark ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-40deg);
            font-size: 72pt;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.03);
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
            font-family: Arial, sans-serif;
        }

        /* ── Certificate wrapper ── */
        .certificate {
            position: relative;
            z-index: 1;
            padding: 14mm 16mm 14mm 16mm;
        }

        /* ── Header: Logo | Branding | QR ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3mm;
            padding-bottom: 3mm;
            height: 18mm;
        }

        .header-logo {
            width: 55mm;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        .header-logo img {
            width: 55mm;
            height: auto;
            object-fit: contain;
        }

        .repeating-logo {
            position: fixed;
            top: 14mm;
            left: 16mm;
            width: 55mm;
            z-index: 210;
            pointer-events: none;
        }

        .repeating-logo img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .repeating-qr {
            position: fixed;
            top: 11mm;
            right: 16mm;
            width: 90px;
            z-index: 210;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 1mm;
        }

        .repeating-header-line {
            position: fixed;
            top: 32mm;
            left: 16mm;
            right: 16mm;
            border-bottom: 1.5px solid #000;
            z-index: 205;
        }

        .qr-verify-text {
            width: 90px;
            font-size: 6pt;
            font-weight: bold;
            line-height: 1.1;
            text-align: center;
            text-transform: uppercase;
        }

        .repeating-header-center {
            position: fixed;
            top: 10mm;
            left: 73mm;
            right: 44mm;
            height: 20mm;
            z-index: 210;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3mm;
        }

        .header-saz-badge {
            width: 17mm;
            height: 17mm;
            flex-shrink: 0;
        }

        .header-saz-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .header-center-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .header-red-number {
            color: #c02424;
            font-size: 14pt;
            font-weight: bold;
            font-family: Arial, sans-serif;
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .header-red-iso {
            color: #c02424;
            font-size: 6.5pt;
            font-weight: bold;
            font-family: Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.15;
            margin-top: 0.5mm;
        }

        /* ── Certificate title ── */
        .cert-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            margin: 3mm 0 1.5mm;
            letter-spacing: 0.5px;
        }

        .cert-title hr {
            border: none;
            border-bottom: 1.5px solid #000;
            margin-top: 1.5mm;
        }

        /* ── Owner / vehicle info boxes ── */
        .info-box {
            border: 1px solid #000;
            padding: 2.5mm 3mm;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 2mm;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #666;
            padding: 1.5px 3px;
        }

        th {
            background: #e0e0e0;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            vertical-align: middle;
        }

        tbody td {
            text-align: center;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            font-size: 8pt;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        .table-footer-note {
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            border-top: 1.5px solid #000;
            padding: 1px 3px;
            line-height: 1.2;
            margin-top: 0.5mm;
            page-break-inside: avoid;
        }

        .table-footer-note.compact {
            font-size: 6.5pt;
            line-height: 1.1;
        }

        /* ── Notes below table ── */
        .notes {
            margin-top: 4mm;
            text-align: center;
            font-size: 8pt;
        }

        .notes-bold {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 1.5mm;
        }

        .notes-normal {
            margin-bottom: 1.5mm;
            line-height: 1.4;
        }

        .notes-url {
            font-weight: bold;
            font-size: 9pt;
            margin: 2mm 0;
        }

        /* ── Traceability / legal text ── */
        .traceability {
            font-size: 7pt;
            margin-top: 0;
        }

        .tr-heading {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 0.5mm;
            display: block;
        }

        .traceability p {
            margin-bottom: 0.6mm;
        }

        /* ── Signatures ── */
        .sig-section {
            display: flex;
            gap: 5mm;
            margin-top: 1mm;
        }

        .sig-block {
            flex: 1;
            font-size: 8pt;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            height: 4.5mm;
            margin-bottom: 1mm;
        }

        .sig-label {
            font-size: 7.5pt;
            color: #333;
        }

        /* ── Reference condition ── */
        .ref-condition {
            font-style: italic;
            font-size: 7.5pt;
            text-align: center;
            margin-top: 0.8mm;
            padding-top: 0.6mm;
            border-top: 0.5px solid #888;
        }

        .table-page {
            display: flex;
            flex-direction: column;
            min-height: 180mm;
        }

        .table-page.continued-page {
            page-break-before: always;
            margin-top: 0;
            padding-top: 36mm;
            min-height: 252mm;
        }

        .table-page.last-table-page {
            min-height: auto;
        }

        .table-page-number {
            margin-top: auto;
            padding-top: 2mm;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: right;
            padding-right: 2mm;
            color: #000;
        }

        .final-page-content {
            min-height: auto;
            display: flex;
            flex-direction: column;
            margin-top: 0;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .final-page-content.new-page {
            page-break-before: always;
            break-before: page;
            padding-top: 37mm;
            min-height: 252mm;
        }
    </style>
</head>

<body>
    {{-- Security border strips --}}
    @php
        $btext = str_repeat('● STANSERV ', 50);
        $logoPath = public_path('logo/sgs logo.jpg');
        $logoMime = file_exists($logoPath) ? (mime_content_type($logoPath) ?: 'image/jpeg') : 'image/jpeg';
        $logoData = file_exists($logoPath)
            ? 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath))
            : asset('logo/sgs logo.jpg');
            
        $qrSvg = SimpleSoftwareIO\QrCode\Facades\QrCode::size(52)->generate(route('calibrations.certificate', $calibration));
        $qrData = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $sazSealPath = public_path('logo/saz-logo.png');
        if (!file_exists($sazSealPath)) {
            $sazSealPath = public_path('logo/saz-seal.svg');
        }
        $sazMime = file_exists($sazSealPath) ? (mime_content_type($sazSealPath) ?: 'image/png') : 'image/png';
        $sazSealData = file_exists($sazSealPath)
            ? 'data:' . $sazMime . ';base64,' . base64_encode(file_get_contents($sazSealPath))
            : asset('logo/saz-logo.png');

        $redPrintNo = $calibration->print_number
            ?? ($calibration->meta['print_number'] ?? null)
            ?? (preg_match('/(\d+)\s*$/', $calibration->calibration_number, $m) ? $m[1] : sprintf('%05d', $calibration->id));
    @endphp
    <div class="border-h border-top">{{ $btext }}</div>
    <div class="border-h border-bottom">{{ $btext }}</div>
    <div class="border-v border-left">{{ $btext }}</div>
    <div class="border-v border-right">{{ $btext }}</div>

    <div class="repeating-logo">
        <img src="{{ $logoData }}" alt="SGS Logo">
    </div>

    <div class="repeating-qr">
        <img src="{{ $qrData }}" alt="QR Code" style="width: 52px; height: 52px; display: block;">
        <div class="qr-verify-text">Scan QR code to verify certificate</div>
    </div>

    <div class="repeating-header-center">
        <div class="header-center-content">
            <div class="header-red-number">{{ $redPrintNo }}</div>
            <div class="header-red-iso">ISO 45001: 2018 OH&S MANAGEMENT SYSTEM CERTIFIED</div>
        </div>
        @if($sazSealData)
        <div class="header-saz-badge">
            <img src="{{ $sazSealData }}" alt="SAZ Tag">
        </div>
        @endif
    </div>

    <div class="repeating-header-line"></div>

    <div class="watermark">STANSERV</div>

    <div class="certificate">

        <div class="header">
        </div>

        {{-- Certificate title --}}
        <div class="cert-title">
            TRUCK CALIBRATION CERTIFICATE NO.: {{ $calibration->calibration_number }}
            <hr>
        </div>

        {{-- Owner box --}}
        <div class="info-box">
            TRUCK OWNER: {{ $truck->owner_name ?? '&mdash;' }}
        </div>

        {{-- Vehicle box --}}
        <div class="info-box">
            TRUCK / TANKER / INTERLINK REG. No.: {{ $truck->reg_info }}
        </div>

        {{-- Readings Table --}}
        @php
            $finalContentStartsNewPage = false;
            $finalContentPageNumber = null;
        @endphp
        @if ($calibration->compartments->count() > 0)
            @php
                $compartments = $calibration->compartments;
                $isSingleCompartment = $compartments->count() === 1;
                $safePrint = $safePrint ?? false;
                $singleCompartmentRowsFirstPage = $safePrint ? 43 : 45;
                $singleCompartmentRowsContinuedPage = $safePrint ? 45 : 54;
                $multiCompartmentRowsFirstPage = $safePrint ? 43 : 44;
                $multiCompartmentRowsContinuedPage = $safePrint ? 45 : 54;
                $singleCompartmentReadingsFirstPage = $singleCompartmentRowsFirstPage * 4;
                $singleCompartmentReadingsContinuedPage = $singleCompartmentRowsContinuedPage * 4;
                $finalContentShareRowsLimit = 50;
            @endphp
            @if ($isSingleCompartment)
                @php
                    $comp = $compartments->first();
                    $readings = $calibration->interpolations->where('compartment_number', $comp->number)->values();
                    $firstPageReadings = $readings->take($singleCompartmentReadingsFirstPage)->values();
                    $continuedPageReadings = $readings
                        ->skip($singleCompartmentReadingsFirstPage)
                        ->values()
                        ->chunk($singleCompartmentReadingsContinuedPage)
                        ->map(fn($chunk) => $chunk->values());
                    $readingPages = collect([$firstPageReadings])
                        ->merge($continuedPageReadings)
                        ->filter(fn($chunk) => $chunk->isNotEmpty())
                        ->values();
                    $readingPagesTotal = $readingPages->count();
                    $lastTableRows = $readingPages->isNotEmpty() ? (int) ceil($readingPages->last()->count() / 4) : 0;
                    $finalContentStartsNewPage = $lastTableRows > $finalContentShareRowsLimit;
                    $certificatePagesTotal = $readingPagesTotal + ($finalContentStartsNewPage ? 1 : 0);
                    $finalContentPageNumber = $finalContentStartsNewPage ? $certificatePagesTotal : null;
                @endphp

                @foreach ($readingPages as $pageReadings)
                    @php
                        $columnLength = (int) ceil($pageReadings->count() / 4);
                    @endphp
                    <div
                        class="table-page {{ $loop->first ? '' : 'continued-page' }} {{ $loop->last ? 'last-table-page' : '' }}">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="8">
                                        COMPARTMENT 1<br>
                                        Reference Height:<br>
                                        {{ $comp->height > 0 ? number_format($comp->height, 0, '.', ' ') : 'N/A' }} mm
                                    </th>
                                </tr>
                                <tr>
                                    <th>Dip (mm)</th>
                                    <th>Volume (L)</th>
                                    <th style="border-left: 2px solid #000;">Dip (mm)</th>
                                    <th>Volume (L)</th>
                                    <th style="border-left: 2px solid #000;">Dip (mm)</th>
                                    <th>Volume (L)</th>
                                    <th style="border-left: 2px solid #000;">Dip (mm)</th>
                                    <th>Volume (L)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < $columnLength; $i++)
                                    @php
                                        $c1 = $pageReadings->get($i);
                                        $c2 = $pageReadings->get($i + $columnLength);
                                        $c3 = $pageReadings->get($i + $columnLength * 2);
                                        $c4 = $pageReadings->get($i + $columnLength * 3);
                                    @endphp
                                    <tr>
                                        <td>{{ $c1 ? number_format($c1->dip_mm, 0, '.', ' ') : '' }}</td>
                                        <td>{{ $c1 ? number_format($c1->volume, 0, '.', ' ') : '' }}</td>
                                        <td style="border-left: 2px solid #000;">
                                            {{ $c2 ? number_format($c2->dip_mm, 0, '.', ' ') : '' }}</td>
                                        <td>{{ $c2 ? number_format($c2->volume, 0, '.', ' ') : '' }}</td>
                                        <td style="border-left: 2px solid #000;">
                                            {{ $c3 ? number_format($c3->dip_mm, 0, '.', ' ') : '' }}</td>
                                        <td>{{ $c3 ? number_format($c3->volume, 0, '.', ' ') : '' }}</td>
                                        <td style="border-left: 2px solid #000;">
                                            {{ $c4 ? number_format($c4->dip_mm, 0, '.', ' ') : '' }}</td>
                                        <td>{{ $c4 ? number_format($c4->volume, 0, '.', ' ') : '' }}</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                        @if ($loop->last)
                            <div class="table-footer-note compact">
                                ALL DIP HEIGHT READINGS MUST BE TAKEN ON LEVEL GROUND &nbsp;|&nbsp;
                                This certificate is valid for two calendar years from date of calibration provided the
                                truck/tanker combination is as recorded above. &nbsp;|&nbsp;
                                <strong>www.sgs-stanserv.com</strong>
                            </div>
                        @endif
                        @if (!$loop->last || $finalContentStartsNewPage)
                            <div class="table-page-number">Page {{ $loop->iteration }} of {{ $certificatePagesTotal }}</div>
                        @endif
                    </div>
                @endforeach
            @else
                @php
                    $readingsByCompartment = $compartments->mapWithKeys(
                        fn($compartment) => [
                            $compartment->number => $calibration->interpolations
                                ->where('compartment_number', $compartment->number)
                                ->values(),
                        ],
                    );
                    $maxRows = $readingsByCompartment->map->count()->max() ?? 0;
                    $firstPageRows =
                        $maxRows > 0 ? collect(range(0, min($multiCompartmentRowsFirstPage, $maxRows) - 1)) : collect();
                    $continuedRows =
                        $maxRows > $multiCompartmentRowsFirstPage
                            ? collect(range($multiCompartmentRowsFirstPage, $maxRows - 1))
                                ->chunk($multiCompartmentRowsContinuedPage)
                                ->map(fn($chunk) => $chunk->values())
                            : collect();
                    $rowPages = collect();
                    if ($firstPageRows->isNotEmpty()) {
                        $rowPages->push($firstPageRows->values());
                    }
                    $rowPages = $rowPages->merge($continuedRows)->values();
                    if ($rowPages->isEmpty()) {
                        $rowPages = collect([collect()]);
                    }
                    $rowPagesTotal = $rowPages->count();
                    $lastTableRows = $rowPages->isNotEmpty() ? $rowPages->last()->count() : 0;
                    $finalContentStartsNewPage = $lastTableRows > $finalContentShareRowsLimit;
                    $certificatePagesTotal = $rowPagesTotal + ($finalContentStartsNewPage ? 1 : 0);
                    $finalContentPageNumber = $finalContentStartsNewPage ? $certificatePagesTotal : null;
                @endphp

                @foreach ($rowPages as $rowPage)
                    <div
                        class="table-page {{ $loop->first ? '' : 'continued-page' }} {{ $loop->last ? 'last-table-page' : '' }}">
                        <table>
                            <thead>
                                <tr>
                                    @foreach ($compartments as $compartment)
                                        <th colspan="2"
                                            style="{{ !$loop->first ? 'border-left: 2px solid #000;' : '' }}">
                                            COMPARTMENT {{ $loop->iteration }}<br>
                                            Reference Height:<br>
                                            {{ $compartment->height > 0 ? number_format($compartment->height, 0, '.', ' ') : 'N/A' }}
                                            mm
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($compartments as $compartment)
                                        <th style="{{ !$loop->first ? 'border-left: 2px solid #000;' : '' }}">Dip<br>mm
                                        </th>
                                        <th>Volume<br>Litres</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rowPage as $rowIndex)
                                    <tr>
                                        @foreach ($compartments as $compartment)
                                            @php
                                                $reading = $readingsByCompartment
                                                    ->get($compartment->number)
                                                    ->get($rowIndex);
                                            @endphp
                                            <td style="{{ !$loop->first ? 'border-left: 2px solid #000;' : '' }}">
                                                {{ $reading ? number_format($reading->dip_mm, 0, '.', ' ') : '' }}
                                            </td>
                                            <td>{{ $reading ? number_format($reading->volume, 0, '.', ' ') : '' }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $compartments->count() * 2 }}">&nbsp;</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if ($loop->last)
                            <div class="table-footer-note">
                                ALL DIP HEIGHT READINGS MUST BE TAKEN ON LEVEL GROUND<br>
                                This certificate is valid for two calendar years from date of calibration provided the
                                truck/tanker combination is as recorded above.<br>
                                <strong>www.sgs-stanserv.com</strong>
                            </div>
                        @endif

                        @if (!$loop->last || $finalContentStartsNewPage)
                            <div class="table-page-number">Page {{ $loop->iteration }} of {{ $certificatePagesTotal }}</div>
                        @endif
                    </div>
                @endforeach

            @endif
        @endif

        <div class="final-page-content {{ $finalContentStartsNewPage ? 'new-page' : '' }}">
            {{-- Traceability / legal notes --}}
            <div class="traceability">
                <span class="tr-heading">ALL COMPARTMENTS CALIBRATED FROM THE BOTTOM WITH BOTTOM LINES COMPLETELY EMPTY.
                    MANIFOLD VOLUME MUST BE INCLUDED AS CAPACITY FOR COMPARTMENT NUMBER ONE.</span>
                <span class="tr-heading">TRACEABILITY OF MEASUREMENT</span>
                <p>THE ACCURACIES OF ALL MEASUREMENTS ARE TRACEABLE TO NATIONAL STANDARDS AS MAINTAINED BY THE TRADE
                    MEASURES (ASSIZE) (AMENDMENT REGULATIONS 1989, THROUGH VERIFICATION CERTIFICATE NUMBER 0010.</p>
                <span class="tr-heading">IN ACCORDANCE WITH THE GOVERNMENT TRADE MEASURES (ASSIZE) REGULATIONS THIS
                    CERTIFICATE MUST NOT BE USED FOR RETAIL PURPOSES.</span>
                <p><strong>COUPLING HEIGHTS:</strong> Fore: 10 mm &nbsp;/&nbsp; Aft: ______ mm</p>
                <p><strong>ALL DIP HEIGHT READINGS MUST BE TAKEN FROM THE MARKED POSITION OF EACH MANHOLE, WITH AIRBAGS
                        ON THE HORSE COMPLETELY EMPTY. (EXTRA CAUTION WHEN CHECKING REFERENCE HEIGHT ON COMPARTMENT
                        4)</strong>
                </p>
            </div>

            {{-- Signatures --}}
            <div class="sig-section">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-label">Date</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-label">Valid Until</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-label">Authorized Signature</div>
                </div>
            </div>

            {{-- Reference condition --}}
            <div class="ref-condition">
                The above volumes are given at reference condition of 20&deg;C and 0 Bar Gauged Pressure.
            </div>

            <div class="table-page-number">Page {{ $certificatePagesTotal }} of {{ $certificatePagesTotal }}</div>

        </div>

    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>
