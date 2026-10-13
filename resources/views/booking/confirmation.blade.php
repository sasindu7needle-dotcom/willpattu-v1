@extends('layout')
@section('title', 'Booking confirmation')
@section('content')
<link rel="stylesheet" href="{{ asset('css/booking.css') }}">
<section class="reservation-page container">
    <ol class="reservation-steps"><li>1 · Choose rooms</li><li>2 · Your details</li><li aria-current="step">3 · Confirmation</li></ol>
    <div class="reservation-confirmation"><span class="reservation-check" aria-hidden="true">✓</span><span class="eyebrow">{{ strtoupper($booking->status) }}</span><h1>{{ $booking->status === 'confirmed' ? 'The wild is waiting.' : 'Your booking was cancelled.' }}</h1><p>{{ $booking->status === 'confirmed' ? 'Your rooms are reserved, '.$booking->name.'. Save your booking reference for check-in.' : 'Contact reservations if you need help making another booking.' }}</p>
        <div class="reservation-reference">Booking reference <strong>{{ $booking->reference }}</strong></div>
        <h2>Makara Resorts</h2><p>{{ $booking->arrival }} → {{ $booking->departure }}<br>{{ $booking->adults }} adults · {{ $booking->children }} children</p>
        @foreach($items as $item)<div class="reservation-line"><span>{{ $item->quantity }} × {{ $item->room_name }}</span><strong>LKR {{ number_format($item->subtotal, 2) }}</strong></div>@endforeach
        <div class="reservation-total"><span>Total · Pay at the property</span><strong>LKR {{ number_format($booking->total, 2) }}</strong></div>
        <p>No payment has been collected online. For changes or cancellation, contact reservations with the reference above.</p>
        <div class="reservation-confirm-actions"><button class="button gold" type="button" onclick="window.print()">Print confirmation</button><a class="button" href="{{ route('page', 'contact') }}">Contact reservations</a><a class="text-link" href="{{ route('home') }}">Back to home</a></div>
    </div>
</section>
@endsection
