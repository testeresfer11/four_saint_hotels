<!DOCTYPE html>
<html>
<head>
    <title>Booking Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        h2, h4 { margin: 0; padding: 5px 0; }
    </style>
</head>
<body>
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ $logoBase64 }}" alt="Hotel Logo" style="max-height: 80px;">
</div>

    <h2>Booking Invoice</h2>
    <p><strong>Reservation Code:</strong> {{ $booking->reservation_code }}</p>
    <p><strong>Status:</strong> {{ $booking->status }}</p>
    <p><strong>Check-in:</strong> {{ $booking->checkin_date }} | <strong>Check-out:</strong> {{ $booking->checkout_date }}</p>

    <hr>

    <h4>Hotel Details</h4>
    <p>
        <strong>{{ $booking->hotel->name }}</strong><br>
        {{ $booking->hotel->address }}, {{ $booking->hotel->city }}, {{ $booking->hotel->country }}<br>
        Phone: {{ $booking->hotel->phone }}<br>
        Email: {{ $booking->hotel->email }}
    </p>

    <h4>Customer Details</h4>
    <p>
        {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}<br>
        Phone: {{ $booking->customer->phone_number }}<br>
        Email: {{ $booking->customer->email }}
    </p>

    <h4>Room Information</h4>
    <p>
        Room: {{ $booking->room_name ?? $booking->roomType->room_name ?? 'N/A' }}<br>
        Guests: {{ $booking->guest_count['adults'] ?? 0 }} Adults, {{ $booking->guest_count['children'] ?? 0 }} Children
    </p>

    <h4>Price Breakdown</h4>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount ({{ $booking->currency }})</th>
                <th>VAT</th>
                <th>City Tax</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $vat = 0;
                $cityTax = 0;
            @endphp

            {{-- Room Prices --}}
            @foreach($booking->bookingPrices as $price)
                <tr>
                    <td>Room ({{ $price->date }})</td>
                    <td>{{ number_format($price->amount, 2) }}</td>
                    <td>{{ number_format($price->vat, 2) }}</td>
                    <td>{{ number_format($price->city_tax, 2) }}</td>
                </tr>
                @php
                    $total += $price->amount;
                    $vat += $price->vat;
                    $cityTax += $price->city_tax;
                @endphp
            @endforeach

            {{-- Services --}}
            @foreach($booking->bookingServices as $service)
                @php
                    $prices = collect($service->bookingServicePrices);
                    $quantity = $prices->count();
                    $totalServiceAmount = $prices->sum('amount');
                    $unitPrice = $quantity > 0 ? ($totalServiceAmount / $quantity) : 0;
                    $startDate = $prices->min('date');
                    $endDate = $prices->max('date');

                    $total += $totalServiceAmount;
                @endphp

                <tr>
                    <td>

                        Service - {{ $service->service_name }}<br>
                        @if($startDate && $endDate)
                            <small>From {{ $startDate }} to {{ $endDate }}</small><br>
                        @endif
                        <small>{{ $quantity }} × {{ number_format($unitPrice, 2) }}</small>
                    </td>
                    <td>{{ number_format($totalServiceAmount, 2) }}</td>
                    <td>0.00</td>
                    <td>0.00</td>
                </tr>
            @endforeach

            {{-- Total --}}
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>{{ number_format($total, 2) }}</strong></td>
                <td><strong>{{ number_format($vat, 2) }}</strong></td>
                <td><strong>{{ number_format($cityTax, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <h4>Comments</h4>
    <p>{!! nl2br(e($booking->comment)) !!}</p>

    <p style="margin-top: 30px;">Thank you for your booking!</p>
</body>
</html>
