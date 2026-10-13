@extends('admin.layout')
@section('title', 'Bookings')
@section('content')
<link rel="stylesheet" href="{{ asset('css/admin-bookings.css') }}">
<section class="editor-heading"><div><span class="admin-eyebrow">RESERVATIONS</span><h1>Guest <em>bookings.</em></h1><p>{{ $confirmedCount }} confirmed bookings · {{ $upcomingCount }} upcoming arrivals</p></div><a class="outline-button" href="{{ route('admin.bookings.inventory') }}">Manage rooms & rates →</a></section>
@include('admin.bookings.notices')
<form method="GET" class="booking-admin-filter"><label>Find a booking<input name="search" value="{{ request('search') }}" maxlength="120" placeholder="Reference, guest name or email"></label><label>Status<select name="status"><option value="">All bookings</option>@foreach(['confirmed', 'cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></label><button class="outline-button" type="submit">Filter bookings</button><a href="{{ route('admin.bookings.index') }}">Reset</a></form>
<section class="editor-panel booking-admin-table"><table><thead><tr><th>Reference / Guest</th><th>Stay</th><th>Guests</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($bookings as $booking)<tr><td><a href="{{ route('admin.bookings.show', $booking->id) }}"><strong>{{ $booking->reference }}</strong></a><small>{{ $booking->name }}<br>{{ $booking->email }}</small></td><td>{{ $booking->arrival }}<small>to {{ $booking->departure }}</small></td><td>{{ $booking->adults }} adults<small>{{ $booking->children }} children</small></td><td>LKR {{ number_format($booking->total, 2) }}<small>Pay at property</small></td><td><span class="booking-admin-badge">{{ ucfirst($booking->status) }}</span></td><td><a class="outline-button" href="{{ route('admin.bookings.show', $booking->id) }}">View →</a></td></tr>
@empty<tr><td colspan="6">No bookings found. Guest reservations will appear here once confirmed.</td></tr>@endforelse
</tbody></table></section><div class="booking-admin-pagination">@if($bookings->previousPageUrl())<a href="{{ $bookings->previousPageUrl() }}">← Previous</a>@endif<span>Page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }}</span>@if($bookings->nextPageUrl())<a href="{{ $bookings->nextPageUrl() }}">Next →</a>@endif</div>
@endsection
