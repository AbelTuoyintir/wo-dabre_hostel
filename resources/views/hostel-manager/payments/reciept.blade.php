<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt #{{ $payment->id }}</title>
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
            --ok-bg: #dcfce7;
            --ok-text: #166534;
            --pending-bg: #fef3c7;
            --pending-text: #92400e;
            --fail-bg: #fee2e2;
            --fail-text: #991b1b;
            --refund-bg: #ede9fe;
            --refund-text: #5b21b6;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 28px 16px;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: var(--text);
            background: radial-gradient(1000px 400px at 10% -10%, #dbeafe 0%, transparent 65%), var(--bg);
        }

        .wrapper {
            max-width: 920px;
            margin: 0 auto;
        }

        .receipt {
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            background: var(--card);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }

        .hero {
            padding: 30px;
            color: #fff;
            background: radial-gradient(1000px 300px at 5% -30%, rgba(255,255,255,0.24), rgba(255,255,255,0)),
                        linear-gradient(120deg, #0f172a 0%, #1d4ed8 44%, #7c3aed 100%);
        }

        .hero-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .hero h1 {
            margin: 6px 0 0;
            font-size: 30px;
            letter-spacing: -0.02em;
        }

        .hero p {
            margin: 4px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .receipt-no {
            background: rgba(255,255,255,0.14);
            padding: 12px 14px;
            border-radius: 12px;
            min-width: 150px;
            text-align: right;
            backdrop-filter: blur(2px);
        }

        .receipt-no .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.85;
        }

        .receipt-no .value {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 800;
        }

        .status-row {
            border-bottom: 1px solid var(--border);
            padding: 14px 30px;
            background: #f8fafc;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .status-pill.completed { background: var(--ok-bg); color: var(--ok-text); border-color: #bbf7d0; }
        .status-pill.pending { background: var(--pending-bg); color: var(--pending-text); border-color: #fde68a; }
        .status-pill.failed { background: var(--fail-bg); color: var(--fail-text); border-color: #fecaca; }
        .status-pill.refunded { background: var(--refund-bg); color: var(--refund-text); border-color: #ddd6fe; }

        .status-meta {
            margin-top: 8px;
            font-size: 13px;
            color: var(--muted);
        }

        .content {
            padding: 26px 30px 30px;
            display: grid;
            gap: 16px;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
        }

        .card h2 {
            margin: 0 0 14px;
            font-size: 16px;
            letter-spacing: -0.01em;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .item .label {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .item .value {
            margin-top: 4px;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            word-break: break-word;
        }

        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }

        .summary {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin: 8px 0;
        }

        .summary-row.muted { color: var(--muted); }
        .summary-row.refund { color: #b91c1c; }

        .divider {
            border-top: 1px solid var(--border);
            margin: 12px 0;
        }

        .total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-weight: 800;
            font-size: 22px;
            color: #059669;
        }

        .footer-note {
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 14px;
            text-align: center;
            font-size: 12px;
            color: var(--muted);
        }

        .actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #475569; color: #fff; }

        @media (max-width: 700px) {
            .hero { padding: 22px; }
            .status-row, .content { padding-left: 18px; padding-right: 18px; }
            .hero-row { flex-direction: column; }
            .receipt-no { text-align: left; min-width: unset; }
            .grid { grid-template-columns: 1fr; }
            .total { font-size: 19px; }
        }

        @media print {
            @page { margin: 12mm; }
            body { background: #fff; padding: 0; }
            .receipt { box-shadow: none; }
            .actions { display: none !important; }
            .hero { background: #1d4ed8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
@php
    $status = strtolower((string)($payment->status ?? 'pending'));
    $booking = $payment->booking;
    $student = $booking?->user;
    $hostel = $booking?->hostel;
    $room = $booking?->room;

    $refundAmount = (float) ($payment->refund_amount ?? 0);
    $amount = (float) ($payment->amount ?? 0);
    $netPaid = max(0, $amount - $refundAmount);

    $statusClass = in_array($status, ['completed', 'pending', 'failed', 'refunded']) ? $status : 'pending';

    $methodDisplay = match($payment->payment_method) {
        'card' => 'Card Payment',
        'mobile_money' => 'Mobile Money',
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash',
        default => ucfirst($payment->payment_method ?? 'N/A')
    };
@endphp

<div class="wrapper">
    <div class="receipt" id="receipt-content">
        <div class="hero">
            <div class="hero-row">
                <div>
                    <p style="margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.2em;opacity:.9;">Official Receipt</p>
                    <h1>Payment Receipt</h1>
                    <p>{{ config('app.name') }} Hostel Management</p>
                </div>
                <div class="receipt-no">
                    <div class="label">Receipt No.</div>
                    <div class="value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>

        <div class="status-row">
            <span class="status-pill {{ $statusClass }}">
                <span style="font-size:8px;">?</span>{{ ucfirst($status) }}
            </span>
            <div class="status-meta">Issued {{ $payment->created_at->format('F d, Y h:i A') }}</div>
        </div>

        <div class="content">
            <section class="card">
                <h2>Transaction Details</h2>
                <div class="grid">
                    <div class="item">
                        <div class="label">Transaction ID</div>
                        <div class="value mono">{{ $payment->transaction_id ?? 'N/A' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Reference</div>
                        <div class="value mono">{{ $payment->reference ?? 'N/A' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Payment Method</div>
                        <div class="value">{{ $methodDisplay }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Currency</div>
                        <div class="value">{{ $payment->currency ?? 'GHS' }}</div>
                    </div>
                </div>
            </section>

            <section class="card">
                <h2>Student & Booking Details</h2>
                <div class="grid">
                    <div class="item">
                        <div class="label">Student Name</div>
                        <div class="value">{{ $student?->name ?? 'N/A' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Student ID</div>
                        <div class="value">{{ $student?->student_id ?? 'N/A' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Email</div>
                        <div class="value">{{ $student?->email ?? 'N/A' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Hostel</div>
                        <div class="value">{{ $hostel?->name ?? 'N/A' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Room</div>
                        <div class="value">{{ $room?->number ?? 'N/A' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Booking Number</div>
                        <div class="value">{{ $booking?->booking_number ?? ('#' . ($payment->booking_id ?? 'N/A')) }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Check-in Date</div>
                        <div class="value">{{ $booking?->check_in_date?->format('M d, Y') ?? $booking?->check_in?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Check-out Date</div>
                        <div class="value">{{ $booking?->check_out_date?->format('M d, Y') ?? $booking?->check_out?->format('M d, Y') ?? 'N/A' }}</div>
                    </div>
                </div>
            </section>

            <section class="summary">
                <h2 style="margin:0 0 12px;font-size:16px;">Payment Summary</h2>
                <div class="summary-row muted">
                    <span>Amount Paid</span>
                    <strong>GHS {{ number_format($amount, 2) }}</strong>
                </div>
                @if($refundAmount > 0)
                    <div class="summary-row refund">
                        <span>Refund Amount</span>
                        <strong>- GHS {{ number_format($refundAmount, 2) }}</strong>
                    </div>
                @endif
                <div class="divider"></div>
                <div class="total">
                    <span>Net Paid</span>
                    <span>GHS {{ number_format($netPaid, 2) }}</span>
                </div>
            </section>

            @if($payment->notes)
                <section class="card">
                    <h2>Notes</h2>
                    <div style="font-size:14px;color:#334155;line-height:1.55;">{{ $payment->notes }}</div>
                </section>
            @endif

            <div class="footer-note">
                <p style="margin:0;">This is a computer-generated receipt and does not require a signature.</p>
                <p style="margin:6px 0 0;">Generated on {{ now()->format('d M Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <div class="actions">
        <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
</div>
</body>
</html>
