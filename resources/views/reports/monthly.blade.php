<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>OmaSync Monthly Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            background: #ffffff;
        }

        .hero {
            background: #0D3B66;
            color: white;
            padding: 28px;
            border-radius: 16px;
        }

        .brand {
            font-size: 13px;
            letter-spacing: 2px;
            color: #BFDBFE;
            font-weight: bold;
        }

        .title {
            font-size: 30px;
            font-weight: bold;
            margin-top: 8px;
        }

        .subtitle {
            color: #DBEAFE;
            margin-top: 6px;
        }

        .month-badge {
            display: inline-block;
            background: #FBBF24;
            color: #111827;
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: bold;
            font-size: 16px;
            margin-top: 18px;
        }

        .grid {
            display: table;
            width: 100%;
            margin-top: 22px;
            border-spacing: 8px;
        }

        .card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            background: #ffffff;
        }

        .card-profit {
            background: #DCFCE7;
            border: 2px solid #22C55E;
        }

        .label {
            color: #6B7280;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .value {
            font-size: 20px;
            font-weight: bold;
            margin-top: 7px;
        }

        .profit {
            color: #16A34A;
        }

        .loss {
            color: #DC2626;
        }

        h2 {
            margin-top: 32px;
            color: #0D3B66;
            font-size: 20px;
        }

        .summary {
            background: #F8FAFC;
            border: 1px solid #E5E7EB;
            border-radius: 14px;
            padding: 18px;
            margin-top: 24px;
        }

        .summary p {
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: #F1F5F9;
            text-align: left;
            padding: 11px;
            font-size: 11px;
            color: #334155;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #E5E7EB;
            padding: 11px;
            font-size: 11px;
        }

        .channel-airbnb { color: #FF5A5F; font-weight: bold; }
        .channel-agoda { color: #B7791F; font-weight: bold; }
        .channel-booking { color: #003B95; font-weight: bold; }
        .channel-direct { color: #16A34A; font-weight: bold; }

        .footer {
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px solid #E5E7EB;
            color: #6B7280;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $monthName = strtoupper(\Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y'));
    @endphp

    <div class="hero">
        <div class="brand">OMASYNC PMS</div>
        <div class="title">Monthly Performance Report</div>
        <div class="subtitle">Financial, occupancy and reservation performance summary.</div>
        <div class="month-badge">{{ $monthName }}</div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="label">Revenue</div>
            <div class="value">RM {{ number_format($revenue, 2) }}</div>
        </div>

        <div class="card">
            <div class="label">Expenses</div>
            <div class="value">RM {{ number_format($expenseTotal, 2) }}</div>
        </div>

        <div class="card card-profit">
            <div class="label">Net Profit</div>
            <div class="value {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                RM {{ number_format($netProfit, 2) }}
            </div>
        </div>

        <div class="card">
            <div class="label">Bookings</div>
            <div class="value">{{ $bookingCount }}</div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="label">Occupancy</div>
            <div class="value">{{ number_format($occupancy, 1) }}%</div>
        </div>

        <div class="card">
            <div class="label">ADR</div>
            <div class="value">RM {{ number_format($adr, 2) }}</div>
        </div>

        <div class="card">
            <div class="label">RevPAR</div>
            <div class="value">RM {{ number_format($revpar, 2) }}</div>
        </div>

        <div class="card">
            <div class="label">Generated</div>
            <div class="value">{{ now()->format('d-m-Y') }}</div>
        </div>
    </div>

    <div class="summary">
        <h2 style="margin-top:0;">Executive Summary</h2>
        <p><strong>{{ $monthName }}</strong> generated <strong>RM {{ number_format($revenue, 2) }}</strong> in revenue.</p>
        <p>Total expenses were <strong>RM {{ number_format($expenseTotal, 2) }}</strong>.</p>
        <p>Net profit for this period is <strong class="{{ $netProfit >= 0 ? 'profit' : 'loss' }}">RM {{ number_format($netProfit, 2) }}</strong>.</p>
        <p>Total confirmed booking records: <strong>{{ $bookingCount }}</strong>.</p>
    </div>

    <h2>Reservations</h2>

    <table>
        <thead>
            <tr>
                <th>Guest</th>
                <th>Channel</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->guest_name }}</td>
                    <td>
                        @if($reservation->channel === 'Airbnb')
                            <span class="channel-airbnb">Airbnb</span>
                        @elseif($reservation->channel === 'Agoda')
                            <span class="channel-agoda">Agoda</span>
                        @elseif($reservation->channel === 'Booking.com')
                            <span class="channel-booking">Booking.com</span>
                        @elseif($reservation->channel === 'Direct')
                            <span class="channel-direct">Direct</span>
                        @else
                            {{ $reservation->channel }}
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($reservation->check_in)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($reservation->check_out)->format('d-m-Y') }}</td>
                    <td><strong>RM {{ number_format($reservation->total_price, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No reservations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Expenses</h2>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Description</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($expenses as $expense)
                <tr>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') }}</td>
                    <td><strong>RM {{ number_format($expense->amount, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No expenses found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Prepared by OmaSync Property Management System · Generated on {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>