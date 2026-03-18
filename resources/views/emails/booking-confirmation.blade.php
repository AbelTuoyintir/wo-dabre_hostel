<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .booking-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .credentials {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #b8e1ff;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>UCC Hostel Booking System</h1>
        <p>Your booking has been confirmed!</p>
    </div>

    <div class="content">
        <h2>Hello {{ $user->name }},</h2>
        
        <p>Thank you for booking with UCC Hostel Booking System. Your payment has been successfully processed and your booking is confirmed.</p>

        @if(isset($is_new_account) && $is_new_account)
        <div class="credentials">
            <h3 style="margin-top: 0; color: #0369a1;">Welcome to UCC Hostel Booking System!</h3>
            <p>An account has been created for you. Here are your login details:</p>
            
            <table style="width: 100%; margin: 15px 0;">
                <tr>
                    <td style="padding: 8px; background: #f0f0f0; font-weight: bold;">Email:</td>
                    <td style="padding: 8px; background: #f0f0f0;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; font-weight: bold;">Password:</td>
                    <td style="padding: 8px;"><strong style="background: #fff3cd; padding: 4px 8px; border-radius: 4px;">{{ $password }}</strong></td>
                </tr>
            </table>
            
            <p style="font-size: 14px; color: #666;">
                <strong> Important:</strong> Please change your password after logging in for security reasons.
            </p>
            
            <a href="{{ $login_url }}" class="button">Login to Your Account</a>
        </div>
        @endif

        <div class="booking-details">
            <h3 style="margin-top: 0; color: #333;">📋 Booking Details</h3>
            
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0;"><strong>Booking Reference:</strong></td>
                    <td style="padding: 8px 0;">{{ $booking->booking_reference }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Hostel:</strong></td>
                    <td style="padding: 8px 0;">{{ $booking->room->hostel->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Room Number:</strong></td>
                    <td style="padding: 8px 0;">{{ $booking->room->number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Check-in Date:</strong></td>
                    <td style="padding: 8px 0;">{{ $booking->check_in->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Check-out Date:</strong></td>
                    <td style="padding: 8px 0;">{{ $booking->check_out->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Total Amount:</strong></td>
                    <td style="padding: 8px 0; font-weight: bold; color: #10b981;">₵{{ number_format($booking->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Payment Reference:</strong></td>
                    <td style="padding: 8px 0;">{{ $booking->payment_reference }}</td>
                </tr>
            </table>
        </div>

        <h3>Important Information</h3>
        <ul style="padding-left: 20px;">
            <li>Please bring a valid ID and your student ID for check-in</li>
            <li>For any inquiries, contact the hostel management</li>
        </ul>

        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ route('student.dashboard') }}" class="button">View My Bookings</a>
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Wo Dare Booking System. All rights reserved.</p>
        <p>University of Cape Coast, Ghana</p>
    </div>
</body>
</html>