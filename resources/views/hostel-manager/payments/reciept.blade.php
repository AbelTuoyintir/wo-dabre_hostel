<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt #{{ $payment->id }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .receipt {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        .details {
            margin-bottom: 20px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .total {
            border-top: 2px solid #333;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .status {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status.completed {
            background: #d4edda;
            color: #155724;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }
        @media print {
            body { background: white; }
            .receipt { box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Hostel Management System</p>
            <p>Payment Receipt</p>
        </div>

        <div class="details">
            <div class="row">
                <span class="label">Receipt No:</span>
                <span class="value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="row">
                <span class="label">Date:</span>
                <span class="value">{{ $payment->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="row">
                <span class="label">Transaction ID:</span>
                <span class="value">{{ $payment->transaction_id ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="details">
            <h3 style="margin:0 0 10px 0; font-size:16px;">Student Information</h3>
            <div class="row">
                <span class="label">Name:</span>
                <span class="value">{{ $payment->booking->user->name ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="label">Student ID:</span>
                <span class="value">{{ $payment->booking->user->student_id ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="label">Email:</span>
                <span class="value">{{ $payment->booking->user->email ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="details">
            <h3 style="margin:0 0 10px 0; font-size:16px;">Booking Details</h3>
            <div class="row">
                <span class="label">Booking ID:</span>
                <span class="value">#{{ $payment->booking_id }}</span>
            </div>
            <div class="row">
                <span class="label">Hostel:</span>
                <span class="value">{{ $payment->booking->hostel->name ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="label">Room:</span>
                <span class="value">{{ $payment->booking->room->number ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="label">Period:</span>
                <span class="value">{{ $payment->booking->check_in->format('d M Y') }} - {{ $payment->booking->check_out->format('d M Y') }}</span>
            </div>
        </div>

        <div class="details">
            <h3 style="margin:0 0 10px 0; font-size:16px;">Payment Details</h3>
            <div class="row">
                <span class="label">Method:</span>
                <span class="value">
                    @php
                        $methodDisplay = match($payment->payment_method) {
                            'card' => '💳 Card Payment',
                            'mobile_money' => '📱 Mobile Money',
                            'bank_transfer' => '🏦 Bank Transfer',
                            'cash' => '💵 Cash',
                            default => ucfirst($payment->payment_method ?? 'N/A')
                        };
                    @endphp
                    {{ $methodDisplay }}
                </span>
            </div>
            @if($payment->payment_method == 'mobile_money')
            <div class="row">
                <span class="label">Mobile Number:</span>
                <span class="value">{{ $payment->mobile_number ?? 'N/A' }}</span>
            </div>
            @endif
            <div class="row total">
                <span class="label">Amount Paid:</span>
                <span class="value">₵{{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <div class="status completed">
            <span>✓ PAYMENT COMPLETED</span>
        </div>

        @if($payment->notes)
        <div class="details" style="margin-top:15px;">
            <div class="row">
                <span class="label">Notes:</span>
                <span class="value">{{ $payment->notes }}</span>
            </div>
        </div>
        @endif

        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>This is a computer generated receipt - no signature required.</p>
            <p>Generated on: {{ now()->format('d M Y h:i A') }}</p>
        </div>

        <div style="text-align: center; margin-top: 20px;" class="no-print">
            <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer;">
                🖨️ Print Receipt
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                ✕ Close
            </button>
        </div>
    </div>
</body>
</html>
