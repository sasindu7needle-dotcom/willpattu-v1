<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content="@yield('description', 'Discover Wilpattu with Makara Resorts. Explore wildlife, safari experiences and thoughtful escapes into nature.')"><title>@yield('title', 'Wilpattu Park') · Makara Resorts</title><link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset('css/park.css') }}"><link rel="stylesheet" href="{{ asset('css/botanical.css') }}"><script defer src="{{ asset('js/park.js') }}"></script></head>
<body class="botanical-theme"><a class="skip" href="#main">Skip to content</a><header class="resort-header">
<div class="header-top">
<div class="header-resort"><button class="menu-toggle" aria-label="Open all pages" aria-expanded="false" aria-controls="site-menu"><span></span><span></span><span></span></button><a class="resort-name" href="{{ route('home') }}">Wilpattu Park</a></div>
<a class="header-logo" href="{{ route('home') }}" aria-label="Wilpattu Park home"><svg class="brand-leaf" viewBox="0 0 40 48" aria-hidden="true"><path d="M20 43V17M20 29C2 28 3 9 3 9s18 0 17 20Zm0-7C37 21 37 3 37 3S19 4 20 22Z" fill="none" stroke="currentColor" stroke-width="1.4"/></svg><span>WILPATTU PARK<small>A MAKARA ESCAPE</small></span></a>
<a class="button header-book" href="{{ route('booking.create') }}">Book now</a>
</div>
<nav class="header-navigation" aria-label="Main navigation"><a href="{{ route('home') }}">Home</a><a href="{{ route('page', 'stay') }}">Villas</a><a href="{{ route('page', 'offers') }}">Offers</a><a href="{{ route('page', 'wildlife') }}">Destination</a><a href="{{ route('page', 'experiences') }}">Experiences</a><a href="{{ route('page', 'gallery') }}">Gallery</a><a href="{{ route('page', 'contact') }}">Contact us</a></nav>
</header>
<div class="site-menu" id="site-menu" hidden><nav aria-label="All pages">@foreach(config('content.pages') as $key=>$entry)<a href="{{ route('page',$key) }}">{{ $entry['kicker'] }}</a>@endforeach</nav></div>
<main id="main">@yield('content')</main>
<section class="closing"><span class="eyebrow">THE WILD IS CALLING</span><h2>Come for the safari.<br>Stay for the <em>feeling.</em></h2><a class="button gold" href="{{ route('page','contact') }}">Plan your escape <span>↗</span></a></section>
<footer class="resort-footer" aria-label="Site footer">
    <div class="resort-footer-main">
        <div class="resort-footer-brand">
            <a class="resort-footer-logo" href="{{ route('home') }}" aria-label="Wilpattu Park home">WILPATTU PARK<span>MAKARA RESORTS</span></a>
            @if(collect(config('park.social_links'))->filter()->isNotEmpty())
                <div class="resort-footer-socials" aria-label="Follow us">
                    @foreach(config('park.social_links') as $network => $url)
                        @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network) }}">{{ ['x' => 'X', 'linkedin' => 'in', 'instagram' => 'IG'][$network] }}</a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        <nav class="resort-footer-navigation" aria-label="Explore Wilpattu Park">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('page', 'stay') }}">Villas</a>
            <a href="{{ route('page', 'offers') }}">Offers</a>
            <a href="{{ route('page', 'wildlife') }}">Destination</a>
            <a href="{{ route('page', 'experiences') }}">Experiences</a>
        </nav>
        <nav class="resort-footer-navigation" aria-label="About and support">
            <a href="{{ route('page', 'about') }}">About us</a>
            <a href="{{ route('page', 'gallery') }}">Gallery</a>
            <a href="{{ route('page', 'faq') }}">FAQ</a>
            <a href="{{ route('page', 'contact') }}">Contact us</a>
        </nav>
        <div class="resort-footer-contact">
            <address>
                <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode(config('park.footer_address')) }}" target="_blank" rel="noopener noreferrer"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a8 8 0 0 0-8 8c0 6 8 12 8 12s8-6 8-12a8 8 0 0 0-8-8Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg><span>{{ config('park.footer_address') }}</span></a>
                @if(config('park.phone'))
                    <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('park.phone')) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 2 4 5-3 3c2 4 3 5 7 7l3-3 5 4c-1 4-3 5-6 4C9 20 4 15 2 8 1 5 2 3 6 2Z"/></svg><span>{{ config('park.phone') }}</span></a>
                @endif
                <a href="mailto:{{ config('park.footer_email') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 5h20v14H2V5Zm1 1 9 7 9-7M3 18l6-6m12 6-6-6" fill="none" stroke="currentColor" stroke-width="1.5"/></svg><span>{{ config('park.footer_email') }}</span></a>
            </address>
            <div class="resort-footer-newsletter" id="newsletter">
                <label for="newsletter-email">Newsletter Subscription</label>
                <form method="POST" action="{{ route('newsletter.subscribe') }}">
                    @csrf
                    <div class="resort-footer-newsletter-field">
                        <input id="newsletter-email" name="newsletter_email" type="email" autocomplete="email" placeholder="Enter your email address" maxlength="254" value="{{ old('newsletter_email') }}" required @if($errors->newsletter->has('newsletter_email')) aria-invalid="true" aria-describedby="newsletter-error" @endif>
                        <button type="submit" aria-label="Subscribe to newsletter"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h17m-6-6 6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.2"/></svg></button>
                    </div>
                    <div class="honeypot" aria-hidden="true"><input name="newsletter_website" tabindex="-1" autocomplete="off" aria-label="Leave empty"></div>
                </form>
                @error('newsletter_email', 'newsletter')<p class="resort-footer-message" id="newsletter-error" role="alert">{{ $message }}</p>@enderror
                @if(session('newsletter_success'))<p class="resort-footer-message" role="status">{{ session('newsletter_success') }}</p>@endif
            </div>
        </div>
    </div>
    <div class="resort-footer-bottom">
        <span>&copy; {{ now()->year }} Wilpattu Park. All Rights Reserved.</span>
        <nav aria-label="Useful information"><a href="{{ route('page', 'sustainability') }}">Sustainability</a><a href="{{ route('page', 'plan-your-stay') }}">Plan your stay</a><a href="{{ route('page', 'journal') }}">Journal</a></nav>
        <span class="resort-footer-credit">Website Designed &amp; Developed by <strong>7needle Pvt Ltd</strong></span>
    </div>
</footer>
@if(config('park.whatsapp'))<a class="whatsapp" href="https://wa.me/{{ preg_replace('/\D/', '', config('park.whatsapp')) }}" target="_blank" rel="noopener">Chat on WhatsApp ↗</a>@endif
<dialog id="lightbox"><button class="lightbox-close" aria-label="Close image">×</button><img id="lightbox-image" alt=""><p id="lightbox-caption"></p></dialog>
</body></html>
