@extends('admin.layout')
@section('title', 'Booking '.$booking->reference)
@section('content')
<link rel="stylesheet" href="{{ asset('css/admin-bookings.css') }}">
<section class="editor-heading"><div><a class="admin-breadcrumb" href="{{ route('admin.bookings.index') }}">Bookings / {{ $booking->reference }}</a><span class="admin-eyebrow">{{ strtoupper($booking->status) }}</span><h1>{{ $booking->name }}</h1><p>Makara Resorts · Booked {{ $booking->created_at }}</p></div><a class="outline-button" href="{{ route('admin.bookings.index') }}">All bookings →</a></section>
@include('admin.bookings.notices')
<div class="booking-admin-details">
<section class="editor-panel"><div class="editor-panel-heading"><h2>Guest & stay</h2></div><dl><dt>Email</dt><dd><a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a></dd><dt>Phone</dt><dd>{{ $booking->phone }}</dd><dt>Check-in / Check-out</dt><dd>{{ $booking->arrival }} → {{ $booking->departure }}</dd><dt>Guests</dt><dd>{{ $booking->adults }} adults · {{ $booking->children }} children</dd><dt>Nationality</dt><dd>{{ $booking->nationality }}</dd><dt>Special requests</dt><dd class="booking-admin-request">{{ $booking->requests ?: 'None provided' }}</dd><dt>Consent recorded</dt><dd>{{ $booking->consented_at }}</dd></dl></section>
<section class="editor-panel"><div class="editor-panel-heading"><h2>Reserved rooms</h2></div>@foreach($items as $item)<div class="booking-admin-room"><strong>{{ $item->quantity }} × {{ $item->room_name }}</strong><p>LKR {{ number_format($item->nightly_rate, 2) }} per room / night</p><p>Subtotal: LKR {{ number_format($item->subtotal, 2) }}</p></div>@endforeach<h2>LKR {{ number_format($booking->total, 2) }}</h2><p>Pay at property · No online payment collected</p>
@if($booking->status === 'confirmed')<form method="POST" action="{{ route('admin.bookings.cancel', $booking->id) }}" onsubmit="return confirm('Cancel this booking and release the rooms?')">@csrf @method('PUT')<button class="outline-button" type="submit">Cancel booking & release rooms</button></form>@else<p>This booking is cancelled. Its rooms have been released.</p>@endif
</section></div>
@endsection
