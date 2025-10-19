@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;

    // Generate QR as SVG instead of PNG (no Imagick or GD needed)
    $bookingId = strtoupper(substr(md5($user->id . $event->event_id), 0, 8));
    $qrData = "EventID:{$event->event_id}|User:{$user->id}|Booking:{$bookingId}";

    // Generate SVG QR Code
    $qrSvg = QrCode::format('svg')
        ->size(160)
        ->errorCorrection('H')
        ->generate($qrData);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Ticket - {{ $event->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .ticket-container {
            width: 700px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px 40px;
        }

        h1 {
            color: #57abd2;
            text-align: center;
            margin-bottom: 10px;
        }

        .details {
            line-height: 1.8;
            margin-top: 15px;
        }

        .details strong {
            color: #57abd2;
        }

        .qr {
            text-align: center;
            margin-top: 25px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 30px;
            color: #666;
        }

        img.banner {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        svg {
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        {{-- Banner --}}
        @if ($event->banner_url && file_exists(public_path($event->banner_url)))
            <img src="{{ public_path($event->banner_url) }}" class="banner">
        @endif

        <h1>🎟️ BookHeaven Event Ticket</h1>

        <div class="details">
            <p><strong>Event:</strong> {{ $event->name }}</p>
            <p><strong>Venue:</strong> {{ $event->venue }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y h:i A') }}</p>
            <p><strong>Attendee:</strong> {{ $user->name }} ({{ $user->email }})</p>
            <p><strong>Booking ID:</strong> #{{ $bookingId }}</p>
        </div>

        {{-- ✅ SVG QR Code (works with DomPDF, no GD or Imagick needed) --}}
        <div class="qr">
            {!! $qrSvg !!}
        </div>

        <div class="footer">
            Please bring this ticket (digital or printed) to the event.  
            Thank you for being a part of <strong>BookHeaven</strong> 💙
        </div>
    </div>
</body>
</html>
