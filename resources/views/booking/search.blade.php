@extends('layout')
@section('title', 'Find your stay')
@section('content')
<link rel="stylesheet" href="{{ asset('css/booking.css') }}">
<script defer src="{{ asset('js/booking.js') }}"></script>
<section class="reservation-page container">
    <span class="eyebrow">MAKARA RESORTS · WILPATTU</span>
    <h1>Find your <em>escape.</em></h1>
    <p class="reservation-lead">Choose your dates. Find your room. Make time for the wild.</p>
    <ol class="reservation-steps"><li aria-current="step">1 · Choose rooms</li><li>2 · Your details</li><li>3 · Confirmation</li></ol>
    @include('booking.errors')
    <form class="reservation-search" method="GET" action="{{ route('booking.search') }}">
        <input type="hidden" name="property" value="makara">
        <label>Property<select aria-label="Property"><option>Makara Resorts</option></select></label>
        <label>Check-in<input type="date" name="arrival" min="{{ now('Asia/Colombo')->toDateString() }}" value="{{ old('arrival', $search['arrival'] ?? '') }}" required></label>
        <label>Check-out<input type="date" name="departure" min="{{ now('Asia/Colombo')->toDateString() }}" value="{{ old('departure', $search['departure'] ?? '') }}" required></label>
        <label>Nationality<select name="nationality" required>@foreach(['Sri Lankan', 'International'] as $nationality)<option @selected(old('nationality', $search['nationality'] ?? 'Sri Lankan') === $nationality)>{{ $nationality }}</option>@endforeach</select></label>
        <label>Adults<input type="number" name="adults" min="1" max="50" value="{{ old('adults', $search['adults'] ?? 2) }}" required></label>
        <label>Children<input type="number" name="children" min="0" max="20" value="{{ old('children', $search['children'] ?? 0) }}" required></label>
        <button class="button gold" type="submit">Check availability</button>
    </form>
    @if($rooms !== null)
        <div class="reservation-results-heading"><div><span class="eyebrow">YOUR STAY</span><h2>Rooms for {{ $nights }} {{ Str::plural('night', $nights) }}</h2><p>{{ $search['arrival'] }} to {{ $search['departure'] }} · {{ $search['adults'] }} adults · {{ $search['children'] }} children</p></div><span class="reservation-pill">{{ $rooms->sum('available') }} rooms available</span></div>
        <form method="POST" action="{{ route('booking.checkout') }}" data-room-selection data-nights="{{ $nights }}" data-guests="{{ $search['adults'] + $search['children'] }}">
            @csrf
            @foreach($search as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
            <div class="reservation-grid">
                <div>
                    @forelse($rooms as $room)
                        <article class="reservation-room">
                            <img src="{{ asset('images/page-stay-villas.webp') }}" alt="Wilpattu accommodation" loading="lazy">
                            <div class="reservation-room-copy"><span class="eyebrow">MAKARA RESORTS</span><h3>{{ $room->name }}</h3><p>{{ $room->description }}</p><p class="reservation-room-meta">Up to {{ $room->capacity }} guests per room · Children count toward capacity</p><span class="reservation-pill">{{ $room->available > 0 ? $room->available.' available for your dates' : 'Sold out for these dates' }}</span><p class="reservation-policy">Pay at the property · Room-only rate · Final price includes applicable room taxes</p></div>
                            <div class="reservation-room-price"><strong>LKR {{ number_format($room->nightly_rate, 2) }}</strong><small>per room / night</small><p>LKR {{ number_format($room->nightly_rate * $nights, 2) }} for {{ $nights }} nights</p>
                            <label>Number of rooms<select name="rooms[{{ $room->id }}]" data-room-quantity data-rate="{{ (int) round($room->nightly_rate * 100) }}" data-capacity="{{ $room->capacity }}" @disabled($room->available === 0)>@for($quantity = 0; $quantity <= $room->available; $quantity++)<option value="{{ $quantity }}" @selected((int) old('rooms.'.$room->id, 0) === $quantity)>{{ $quantity }}</option>@endfor</select></label></div>
                        </article>
                    @empty
                        <div class="reservation-empty"><h2>Stays are being prepared.</h2><p>Online rooms will appear here when reservations opens availability.</p><a class="button gold" href="{{ route('page', 'contact') }}">Contact reservations</a></div>
                    @endforelse
                    @if($rooms->isNotEmpty() && $rooms->sum('available') === 0)<div class="reservation-empty"><h3>No rooms available for these dates</h3><p>Try different dates above, or ask our reservations team for help.</p><a class="text-link" href="{{ route('page', 'contact') }}">Contact reservations</a></div>@endif
                </div>
                <aside class="reservation-summary"><span class="eyebrow">YOUR SELECTION</span><h3>A stay to look forward to.</h3><p data-selection-count aria-live="polite">Choose your rooms to see the total.</p><div class="reservation-total"><span>Total for {{ $nights }} nights</span><strong data-selection-total>LKR 0.00</strong></div><p class="reservation-policy">No online payment required. Your rooms are reserved when you confirm the next step. Cancel through our reservations team before check-in.</p><button class="button gold" type="submit" @disabled($rooms->sum('available') === 0)>Continue to guest details →</button><p data-capacity-warning role="status"></p></aside>
            </div>
        </form>
    @else
        <div class="reservation-empty"><h2>Your next adventure starts here.</h2><p>Search your dates to see available room types, room counts and the full price for your stay.</p><p>For stays over 30 nights, contact reservations.</p></div>
    @endif
</section>
@endsection
