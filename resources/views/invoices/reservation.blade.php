<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reservation Invoice</title>

    <style>
        @page {
            margin: 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 11px;
            margin: 0;
        }

        .watermark {
            position: fixed;
            top: 36%;
            left: 11%;
            font-size: 80px;
            font-weight: 900;
            color: rgba(13, 59, 102, 0.045);
            transform: rotate(-25deg);
            z-index: -1;
        }

        .header {
            background: #0D3B66;
            color: #ffffff;
            padding: 18px 20px;
            border-radius: 14px;
        }

        .logo {
            font-size: 30px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .tagline {
            font-size: 9px;
            letter-spacing: 2.5px;
            color: #BFDBFE;
            margin-top: 2px;
            font-weight: bold;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 12px;
        }

        .invoice-no {
            display: inline-block;
            background: #FBBF24;
            color: #111827;
            padding: 7px 13px;
            border-radius: 999px;
            font-weight: bold;
            margin-top: 9px;
            font-size: 11px;
        }

        .section {
            margin-top: 11px;
            padding: 13px;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            background: #ffffff;
        }

        .summary {
            background: #F8FAFC;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            color: #0D3B66;
            margin-bottom: 10px;
        }

        .grid {
            display: table;
            width: 100%;
        }

        .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .label {
            color: #6B7280;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .value {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table td {
            padding: 4px 0;
            border: none;
            vertical-align: top;
        }

        table.invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.invoice-table th {
            background: #F1F5F9;
            color: #334155;
            text-align: left;
            padding: 9px;
            font-size: 9px;
            text-transform: uppercase;
        }

        table.invoice-table td {
            border-bottom: 1px solid #E5E7EB;
            padding: 9px;
            font-size: 10px;
        }

        .total-box {
            margin-top: 14px;
            text-align: right;
        }

        .total-label {
            color: #6B7280;
            font-weight: bold;
            font-size: 10px;
        }

        .total {
            font-size: 25px;
            font-weight: bold;
            color: #16A34A;
            margin-top: 3px;
        }

        .status-pill {
            display: inline-block;
            background: #DCFCE7;
            color: #15803D;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
        }

        .footer {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #E5E7EB;
            color: #6B7280;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="watermark">OMASYNC</div>

    <div class="header">
        <div class="logo">OMASYNC</div>
        <div class="tagline">PROPERTY MANAGEMENT SYSTEM</div>

        <div class="invoice-title">Reservation Invoice</div>
        <div class="invoice-no">{{ $invoiceNumber }}</div>
    </div>

    <div class="section summary">
        <div class="section-title">Invoice Summary</div>

        <div class="grid">
            <div class="col">
                <table class="mini-table">
                    <tr>
                        <td>
                            <div class="label">Invoice No</div>
                            <div class="value">{{ $invoiceNumber }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="label">Issue Date</div>
                            <div class="value">{{ now()->format('d-m-Y') }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="label">Status</div>
                            <div class="value">
                                <span class="status-pill">{{ $reservation->status }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col">
                <table class="mini-table">
                    <tr>
                        <td>
                            <div class="label">Guest</div>
                            <div class="value">{{ $reservation->guest_name }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="label">Property</div>
                            <div class="value">{{ $reservation->property->name ?? '-' }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="label">Total Amount</div>
                            <div class="value">RM {{ number_format($reservation->total_price, 2) }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Booking Details</div>

        <div class="grid">
            <div class="col">
                <div class="label">Guest Name</div>
                <div class="value">{{ $reservation->guest_name }}</div>

                <div class="label">Channel</div>
                <div class="value">{{ $reservation->channel }}</div>
            </div>

            <div class="col">
                <div class="label">Check In</div>
                <div class="value">{{ \Carbon\Carbon::parse($reservation->check_in)->format('d-m-Y') }}</div>

                <div class="label">Check Out</div>
                <div class="value">{{ \Carbon\Carbon::parse($reservation->check_out)->format('d-m-Y') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Invoice Item</div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Stay Dates</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>
                        <strong>Accommodation Booking</strong><br>
                        {{ $reservation->property->name ?? '-' }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($reservation->check_in)->format('d-m-Y') }}
                        -
                        {{ \Carbon\Carbon::parse($reservation->check_out)->format('d-m-Y') }}
                    </td>

                    <td>
                        <strong>RM {{ number_format($reservation->total_price, 2) }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-label">Total Amount</div>
            <div class="total">RM {{ number_format($reservation->total_price, 2) }}</div>
        </div>
    </div>

    <div class="footer">
        Generated by OmaSync Property Management System · {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>