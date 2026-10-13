@if($errors->any())
<div class="notice error" role="alert"><strong>Please review your booking.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul><a href="{{ route('booking.create') }}">Check availability again</a></div>
@endif
