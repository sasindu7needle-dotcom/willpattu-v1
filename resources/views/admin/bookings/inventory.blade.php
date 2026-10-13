@extends('admin.layout')
@section('title', 'Room inventory')
@section('content')
<link rel="stylesheet" href="{{ asset('css/admin-bookings.css') }}">
<section class="editor-heading"><div><a class="admin-breadcrumb" href="{{ route('admin.bookings.index') }}">Bookings / Inventory</a><span class="admin-eyebrow">ROOMS & RATES</span><h1>Open the doors to <em>your stay.</em></h1><p>Add actual room counts and final nightly rates in LKR. Published rooms appear in guest availability searches.</p></div><a class="outline-button" href="{{ route('booking.create') }}" target="_blank" rel="noopener">Preview guest booking →</a></section>
@include('admin.bookings.notices')
@foreach($rooms as $room)
<form class="admin-editor-form editor-panel booking-admin-inventory" method="POST" action="{{ route('admin.bookings.rooms.update', $room->id) }}">@csrf @method('PUT')
<div class="editor-panel-heading"><h2>{{ $room->name }}</h2><span>{{ $room->is_active ? 'Published' : 'Hidden' }}</span></div>
@include('admin.bookings.room-fields', ['room' => $room, 'useOld' => (int) old('room_id') === $room->id])
<input type="hidden" name="room_id" value="{{ $room->id }}"><button class="outline-button" type="submit">Save room & rates</button></form>
@endforeach
<form class="admin-editor-form editor-panel booking-admin-inventory" method="POST" action="{{ route('admin.bookings.rooms.store') }}">@csrf
<div class="editor-panel-heading"><h2>Add a room type</h2><span>Rates include applicable room taxes; meals and extras excluded</span></div>
@include('admin.bookings.room-fields', ['room' => null, 'useOld' => !old('room_id')])
<button class="outline-button" type="submit">Add room type</button></form>
@endsection
