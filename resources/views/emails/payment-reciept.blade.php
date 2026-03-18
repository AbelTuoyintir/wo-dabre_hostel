<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
        }
        .receipt-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .label {
            font-weight: 600;
            color: #555;
        }
        .value {
            color: #333;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #10b981;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background: #10b981;
            color: white;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Receipt</h1>
            <p>UCC Hostel Booking System</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>

            <p>Thank you for your payment. Your transaction has been completed successfully. Below are the details of your payment:</p>

            <div class="receipt-details">
                <div class="detail-row">
                    <span class="label">Receipt Number:</span>
                    <span class="value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="detail-row">
                    <span class="label">Transaction Reference:</span>
                    <span class="value">{{ $payment->reference }}</span>
                </div>

                <div class="detail-row">
                    <span class="label">Payment Date:</span>
                    <span class="value">{{ $payment->created_at->format('F d, Y h:i A') }}</span>
                </div>

                <div class="detail-row">
                    <span class="label">Payment Method:</span>
                    <span class="value">
                        @php
                            $methodDisplay = match($payment->payment_method) {
                                'card' => '💳 Card Payment',
                                'mobile_money' => 'Mobile Money',
                                'bank_transfer' => 'Bank Transfer',
                                default => ucfirst($payment->payment_method ?? 'N/A')
                            };
                        @endphp
                        {{ $methodDisplay }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="label">Payment Type:</span>
                    <span class="value">
                        @if($payment->booking_id)
                            Booking Payment
                        @else
                            Student Fee Payment
                        @endif
                    </span>
                </div>

                <div class="detail-row">
                    <span class="label">Status:</span>
                    <span class="value">
                        <span class="status-badge">COMPLETED</span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="label">Amount Paid:</span>
                    <span class="value amount">₵{{ number_format($payment->amount, 2) }}</span>
                </div>

                <div class="detail-row">
                    <span class="label">Currency:</span>
                    <span class="value">{{ $payment->currency }}</span>
                </div>
            </div>

            @if($payment->booking_id && $payment->booking)
                <div class="receipt-details">
                    <h3 style="margin-top: 0;">Booking Details</h3>

                    <div class="detail-row">
                        <span class="label">Hostel:</span>
                        <span class="value">{{ $payment->booking->hostel->name ?? 'N/A' }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="label">Room:</span>
                        <span class="value">{{ $payment->booking->room->number ?? 'N/A' }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="label">Check In:</span>
                        <span class="value">{{ $payment->booking->check_in->format('M d, Y') }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="label">Check Out:</span>
                        <span class="value">{{ $payment->booking->check_out->format('M d, Y') }}</span>
                    </div>
                </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ route('student.payments.receipt', $payment) }}" class="button">
                    View Full Receipt Online
                </a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                <strong>Important:</strong> This is a computer-generated receipt. No signature is required.
                Please keep this email for your records.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} University of Cape Coast Hostel Booking System. All rights reserved.</p>
            <p style="font-size: 12px;">For any inquiries, please contact support@ucchostels.com</p>
        </div>
    </div>
</body>
</html>
