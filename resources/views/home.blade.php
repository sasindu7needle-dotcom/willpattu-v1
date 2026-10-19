@extends('layout')
@section('title', 'Wild by Nature. Luxury by Choice.')
@section('description', $page['intro'])
@section('content')
@php
    $useLivingAnimation = $animationSettings['animation_enabled'] && !$animationSettings['animation_path'];
    $heroTitleWords = preg_split('/\s+/', trim($page['title'])) ?: [];
    $heroTitleAccent = array_pop($heroTitleWords) ?? '';
    $heroTitleLead = implode(' ', $heroTitleWords);
    $discoverySymbols = ['safaris' => '⌁', 'stay' => '⌂', 'dining' => '♧', 'experiences' => '☼', 'wildlife' => '❧', 'offers' => '✧'];
    $discoveryImages = [
        'safaris' => 'images/discover-safari.webp',
        'stay' => 'images/discover-accommodation.webp',
        'dining' => 'images/discover-dining.webp',
        'experiences' => 'images/discover-excursions.webp',
        'wildlife' => 'images/discover-wildlife.webp',
        'offers' => 'images/discover-offers.webp',
    ];
@endphp
<div class="wildlife-journey" data-wildlife-journey data-living-animation="{{ $useLivingAnimation ? 'true' : 'false' }}"><div class="wildlife-stage"><section class="hero"><img class="hero-photo" src="{{ asset($useLivingAnimation ? 'images/Makara_resorts_alankuda_beach_excursion_wilpattu_6.jpg' : 'images/wilpattu-forest-plate.png') }}" alt="{{ $useLivingAnimation ? $animationSettings['animation_alt'] : 'A forest trail in Wilpattu National Park' }}" fetchpriority="high"><div class="hero-shade"></div>@if($animationSettings['animation_enabled'])<div class="leopard-actor" @if($useLivingAnimation) hidden @endif role="img" aria-label="{{ $animationSettings['animation_alt'] }}"><div class="leopard-shadow"></div><div class="leopard-sprite" data-animation-src="{{ $animationUrl }}" style="background-image: url('{{ $animationUrl }}')"></div></div>@endif<div class="hero-content"><div class="hero-copy"><span class="eyebrow">{{ $page['kicker'] }}</span><h1>@if ($heroTitleLead !== ''){{ $heroTitleLead }}<br>@endif<em>{{ $heroTitleAccent }}</em></h1><p>{!! nl2br(e($page['intro'])) !!}</p></div><form class="hero-booking" action="{{ route('booking.search') }}" method="GET" aria-label="Book your stay" data-hero-booking>
<input type="hidden" name="adults" value="2"><input type="hidden" name="children" value="0">

<label class="booking-field"><span class="booking-label">Select property</span><select name="property" aria-label="Select property" required><option value="" disabled selected>Select property</option><option value="makara">Makara Resorts</option></select></label>
<label class="booking-field booking-date"><span>Check-in</span><input type="date" name="arrival" aria-label="Check-in" min="{{ now('Asia/Colombo')->toDateString() }}" required></label>
<label class="booking-field booking-date"><span>Check-out</span><input type="date" name="departure" aria-label="Check-out" min="{{ now('Asia/Colombo')->toDateString() }}" required></label>
<label class="booking-field"><span class="booking-label">Nationality</span><select name="nationality" data-booking-nationality aria-label="Nationality" required><option value="" disabled selected>Nationality</option><option value="Sri Lankan">Sri Lankan</option><option value="International">International</option></select></label>
<label class="booking-field"><span class="booking-label">Promo code</span><input type="text" name="promo_code" data-booking-promo aria-label="Promo code" placeholder="Promo code" maxlength="50"></label>
<button class="booking-submit" type="submit">Book now</button>
</form></div><div class="hero-note"><span class="eyebrow">A PLACE APART</span><p>Where the forest whispers.<br>And the wild feels close.</p></div><div class="hero-strip"><span>◈ &nbsp; Thoughtful stays</span><span>⌁ &nbsp; Guided safari experiences</span><span>❧ &nbsp; Wildlife encounters</span><span>✧ &nbsp; Sri Lankan hospitality</span></div><a class="scroll-note" href="#discover">EXPLORE WILPATTU ↓</a></section>
<section id="discover" class="discover container"><div class="section-heading"><span class="eyebrow">EXPERIENCES THAT STAY WITH YOU</span><h2>Discover <em>Wilpattu</em></h2><span class="ornament">──── ❧ ────</span></div><div class="discovery-grid">@foreach($page['items'] as $item)<a class="discovery-card" href="{{ route('page', $item['text']) }}"><img src="{{ asset($discoveryImages[$item['text']] ?? 'images/Makara_resorts_alankuda_beach_excursion_wilpattu_'.$item['image'].'.jpg') }}" alt="{{ $item['title'] }} experience in Wilpattu" loading="lazy"><div><span class="card-symbol">{{ $discoverySymbols[$item['text']] ?? '✧' }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['meta'] }}</p></div></a>@endforeach</div>@if(empty($page['items']))<p class="muted">Featured experiences will be added soon.</p>@endif</section>
</div></div>
<section class="planning container"><div class="planning-copy"><span class="eyebrow">YOUR JOURNEY, THOUGHTFULLY PLANNED</span><h2>A little closer to your <em>escape.</em></h2><p>Find the right season, discover the park, and arrive ready for something extraordinary.</p><a class="button green" href="{{ route('page','plan-your-stay') }}">Plan your stay <span>↗</span></a></div><div class="planning-features"><span>☼<small>Best time<br>to visit</small></span><span>⌁<small>Safari<br>essentials</small></span><span>⌖<small>Getting<br>here</small></span></div><img src="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_13.jpg') }}" alt="A natural lake surrounded by Wilpattu forest" loading="lazy"></section>
<section class="story container"><img src="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_3.jpg') }}" alt="Leopard resting among the trees" loading="lazy"><div><span class="eyebrow">WILD BY NATURE. LUXURY BY CHOICE.</span><h2>Some places are visited.<br>Others are <em>felt.</em></h2><p>A trail through the forest. A moment beside a still lake. An encounter you never expected. Let Wilpattu become part of your story.</p><a class="text-link" href="{{ route('page','about') }}">Our story <span>↗</span></a></div></section>
<section class="journal-section container"><div class="section-heading"><span class="eyebrow">NOTES FROM THE WILDERNESS</span><h2>A field guide to <em>wonder.</em></h2></div><div class="three-grid">@foreach(config('content.articles') as $key=>$article)<a class="editorial-card" href="{{ route('article',$key) }}"><img src="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_'.$article['image'].'.jpg') }}" alt="Wilpattu forest and lake" loading="lazy"><span class="eyebrow">FIELD NOTES</span><h3>{{ $article['title'] }}</h3><span class="text-link">Read the story ↗</span></a>@endforeach</div></section>
@endsection
