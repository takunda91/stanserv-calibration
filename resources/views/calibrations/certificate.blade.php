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
                margin-top: 25mm;
                margin-bottom: 25mm;
                margin-left: 10mm;
                margin-right: 10mm;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page-number:after {
                content: "Page " counter(page);
            }
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 0 15mm;
            box-sizing: border-box;
        }

        .footer-content {
            font-size: 7pt;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px 4px;
            text-align: right;
        }
        th {
            text-align: center;
            background-color: #f0f0f0;
            vertical-align: middle;
        }
        /* Repeat header on new pages */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }

        .traceability-section {
            margin-top: 20px;
            font-size: 8pt;
            page-break-inside: avoid;
        }
        .traceability-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .traceability-text {
            margin-bottom: 5px;
            text-align: justify;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            font-size: 9pt;
            position: relative;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 30%;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 5px;
        }
        .seal-container {
            position: absolute;
            bottom: 0;
            right: 0;
            z-index: 5;
            opacity: 0.8;
            transform: rotate(-15deg);
            pointer-events: none;
        }
        .seal {
            width: 40mm;
            height: 40mm;
            background-color: rgba(180, 0, 0, 0.1); /* Transparent red */
            border: 3px solid #b00;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #b00;
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #b00;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(0, 0, 0, 0.03);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container">
            <div class="watermark">STANSERV</div>
            
            <!-- Header Section -->
            <div style="text-align: center; margin-bottom: 5mm; position: relative;">
                <!-- QR Code -->
                <div style="position: absolute; top: 0; right: 0;">
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($calibration->calibration_number) !!}
                </div>

                <div style="font-size: 24pt; font-weight: bold; letter-spacing: 2px;">STANSERV</div>
                <div style="font-size: 10pt; color: #666; margin-top: 2px;">
                    Stanserv Genuine Services (Pvt) Ltd. | ISO 45001: 2018 OH&S MANAGEMENT SYSTEM CERTIFIED
                </div>
                
                <div style="text-align: left; margin-top: 5mm; font-weight: bold; font-size: 12pt;">
                    TRUCK CALIBRATION CERTIFICATE No.: {{ $calibration->calibration_number }}
                </div>
                <div style="border-bottom: 2px solid #000; margin-top: 2px;"></div>
            </div>

            <!-- Truck Details -->
            <div style="border: 1px solid #000; padding: 5px; margin-bottom: 5mm; font-weight: bold;">
                <div style="display: flex; justify-content: space-between;">
                    <div>TRUCK OWNER: {{ $truck->owner_name ?? 'QUEST COM LOGISTICS (Pvt) Ltd' }}</div>
                    <div class="page-number"></div>
                </div>
                <div style="margin-top: 5px;">
                    TRUCK / TANKER / INTERLINK REG. No.: {{ $truck->reg_info }}
                </div>
            </div>

            <!-- Readings Table -->
            @if($calibration->compartments->count() > 0)
                <table>
                    <thead>
                        <tr>
                            @foreach($calibration->compartments as $compartment)
                                <th colspan="2">
                                    COMPARTMENT {{ $loop->iteration }}<br>
                                    Ref. Height:<br>
                                    {{ $compartment->height > 0 ? number_format($compartment->height, 0) : 'N/A' }} mm
                                </th>
                            @endforeach
                        </tr>
                        <tr class="subheader">
                            @foreach($calibration->compartments as $compartment)
                                <th>Dip<br>mm</th>
                                <th>Volume<br>Litres</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="{{ $calibration->compartments->count() * 2 }}" style="border: none;">
                                <div class="footer-content">
                                    <p style="font-weight: bold; margin: 2px 0;">ALL DIP HEIGHT READINGS MUST BE TAKEN ON LEVEL GROUND</p>
                                    <p style="margin: 2px 0;">This certificate is valid for two calendar years from date of calibration provided the truck/tanker combination is as recorded above.</p>
                                    <p style="font-weight: bold; margin: 2px 0;">www.sgs-stanserv.com</p>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        @php
                            $maxRows = $calibration->interpolations->groupBy('compartment_number')->map->count()->max() ?? 0;
                        @endphp

                        @for($i = 0; $i < $maxRows; $i++)
                            <tr>
                                @foreach($calibration->compartments as $compartment)
                                    @php
                                        $reading = $calibration->interpolations
                                            ->where('compartment_number', $compartment->number)
                                            ->values()
                                            ->get($i);
                                    @endphp
                                    <td>{{ $reading->dip_mm ?? '' }}</td>
                                    <td>{{ $reading->volume ?? '' }}</td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            @endif

            <div class="traceability-section">
                <div style="font-weight: bold; text-decoration: underline; margin-bottom: 5px;">
                    ALL COMPARTMENTS CALIBRATED FROM THE BOTTOM WITH THE BOTTOM LINES COMPLETELY EMPTY AND MANIFOLD VOLUME MUST BE INCLUDED AS CAPACITY FOR COMPARTMENT NUMBER ONE.
                </div>
                
                <div style="font-weight: bold; text-decoration: underline; margin-bottom: 5px;">
                    TRACEABILITY OF MEASUREMENT
                </div>
                
                <p style="margin-bottom: 10px;">
                    THE ACCURACIES OF ALL MEASUREMENTS ARE TRACEABLE TO NATIONAL STANDARDS AS MAINTAINED BY THE TRADE MEASURES (ASSIZE) (AMENDMENT REGULATIONS 1989, THROUGH VERIFICATION CERTIFICATE NUMBER 0010.
                </p>
                
                <div style="font-weight: bold; text-decoration: underline; margin-bottom: 15px;">
                    IN ACCORDANCE WITH THE GOVERNMENT TRADE MEASURES (ASSIZE) REGULATIONS THIS CERTIFICATE MUST NOT BE USED FOR RETAIL PURPOSES.
                </div>
                
                <div style="margin-bottom: 15px;">
                    <span style="font-weight: bold;">COUPLING HEIGHTS:</span> Fore: 10 mm / Aft: ______ mm
                </div>
                
                <div style="font-weight: bold; margin-bottom: 20px;">
                    ALL DIP HEIGHT READINGS MUST BE TAKEN AT ALL TIMES FROM THE MARKED POSITION OF EACH MANHOLE, WITH AIRBAGS ON THE HORSE COMPLETELY EMPTY. (EXTRA CAUTION SHOULD BE TAKEN WHEN CHECKING REFERENCE HEIGHT ON COMPARTMENT NUMBER 4)
                </div>
            </div>

            <!-- Signatures -->
            <div class="signatures">
                <div class="seal-container">
                    <div class="seal">
                        <div>
                            STANSERV<br>
                            GENUINE<br>
                            SERVICES
                        </div>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Authorized Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Date: {{ $calibration->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Valid Until: {{ $calibration->created_at->addYears(2)->format('d/m/Y') }}</div>
                </div>
            </div>
    </div>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
