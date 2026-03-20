<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .booking-details { background: white; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Booking Confirmation</h1>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            
            @if(isset($is_new_account) && $is_new_account)
            <div style="background-color: #e0f2fe; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #0369a1; margin-top: 0;">🎉 Welcome to UCC Hostels!</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Password:</strong> <span style="background: #fef9c3; padding: 3px 8px; border-radius: 3px;">{{ $password }}</span></p>
                <p style="font-size: 14px; color: #666;">Please change your password after logging in.</p>
                <p><a href="{{ $login_url }}" style="background: #0284c7; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Login to Your Account</a></p>
            </div>
            @endif
            
            <div class="booking-details">
                <h3 style="margin-top: 0;">Booking Details</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0;"><strong>Booking Reference:</strong></td>
                        <td style="padding: 8px 0;">{{ $booking->booking_reference ?? $booking->booking_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Hostel:</strong></td>
                        <td style="padding: 8px 0;">{{ $booking->room->hostel->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Room:</strong></td>
                        <td style="padding: 8px 0;">{{ $booking->room->number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Check-in:</strong></td>
                        <td style="padding: 8px 0;">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Check-out:</strong></td>
                        <td style="padding: 8px 0;">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Total Amount:</strong></td>
                        <td style="padding: 8px 0; font-weight: bold; color: #059669;">₵{{ number_format($booking->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
            
            <p>Thank you for choosing UCC Hostels. We look forward to hosting you!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} UCC Hostel Booking System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>