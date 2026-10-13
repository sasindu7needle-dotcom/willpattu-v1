@extends('layout')
@section('title', 'Complete your booking')
@section('content')
<link rel="stylesheet" href="{{ asset('css/booking.css') }}">
<script defer src="{{ asset('js/booking.js') }}"></script>
<section class="reservation-page container">
    <span class="eyebrow">ONE STEP CLOSER TO THE WILD</span><h1>Your <em>stay.</em> Your details.</h1>
    <ol class="reservation-steps"><li><a href="{{ route('booking.search', $search) }}">1 · Choose rooms</a></li><li aria-current="step">2 · Your details</li><li>3 · Confirmation</li></ol>
    @include('booking.errors')
    <form method="POST" action="{{ route('booking.store') }}" class="reservation-grid" data-confirm-booking>
        @csrf
        @foreach($search as $key => $value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
        @foreach($selectedRooms as $room)<input type="hidden" name="rooms[{{ $room->id }}]" value="{{ $room->quantity }}">@endforeach
        <input type="hidden" name="booking_token" value="{{ $token }}">
        <div class="reservation-details"><span class="eyebrow">LEAD GUEST</span><h2>Who is joining us?</h2><p>Use the details of the guest who will check in.</p>
            <div class="form-grid">
                <label>Full name *<input name="name" autocomplete="name" maxlength="120" value="{{ old('name') }}" required></label>
                <label>Email address *<input name="email" type="email" autocomplete="email" maxlength="254" value="{{ old('email') }}" required></label>
                <label>Phone number *<input name="phone" type="tel" autocomplete="tel" maxlength="30" value="{{ old('phone') }}" required></label>
            </div>
            <label>Special requests<textarea name="requests" rows="4" maxlength="2000" placeholder="Arrival time, accessibility needs or anything we should know. Requests are subject to availability.">{{ old('requests') }}</textarea></label>
            <div class="honeypot" aria-hidden="true"><input name="website" tabindex="-1" autocomplete="off" aria-label="Leave empty"></div>
            <label class="checkbox"><input name="consent" type="checkbox" value="1" required @checked(old('consent'))> I agree to the booking terms below and to be contacted using these details.</label>
            <div class="reservation-terms"><h3>Before you confirm</h3><p>Pay the displayed total at the property in LKR. Rates include applicable room taxes. Meals, safaris and extras are arranged separately. All guests, including children, count toward room capacity.</p><p>To cancel before check-in, contact reservations with your booking reference. Special requests are subject to availability. This booking confirms your selected rooms; no payment is collected online.</p></div>
        </div>
        <aside class="reservation-summary"><span class="eyebrow">REVIEW YOUR BOOKING</span><h3>Makara Resorts</h3><p>{{ $search['arrival'] }} → {{ $search['departure'] }}<br>{{ $nights }} nights · {{ $search['adults'] }} adults · {{ $search['children'] }} children<br>{{ $search['nationality'] }}</p>
            @foreach($selectedRooms as $room)<div class="reservation-line"><span>{{ $room->quantity }} × {{ $room->name }}<small>LKR {{ number_format($room->nightly_rate, 2) }} / room / night</small></span><strong>LKR {{ number_format($room->subtotal / 100, 2) }}</strong></div>@endforeach
            <div class="reservation-total"><span>Total · {{ $roomCount }} rooms</span><strong>LKR {{ number_format($total / 100, 2) }}</strong></div><p class="reservation-policy">Pay at the property · No online payment required</p>
            <button class="button gold" type="submit">Confirm booking →</button>
            <a class="reservation-back" href="{{ route('booking.search', $search) }}">Change dates or rooms</a>
        </aside>
    </form>
</section>
@endsection
