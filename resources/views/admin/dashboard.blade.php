@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <section class="dashboard-heading">
        <div>
            <span class="admin-eyebrow">CONTENT OVERVIEW</span>
            <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, <em>admin.</em></h1>
            <p>Everything guests discover about Wilpattu will be managed from here.</p>
        </div>
        <a class="outline-button" href="{{ route('home') }}" target="_blank" rel="noopener">Preview website <span>↗</span></a>
    </section>

    <section class="stat-grid" aria-label="Website summary">
        <article class="stat-card"><span class="stat-symbol">□</span><div><strong>{{ $pageCount }}</strong><span>Website pages</span></div></article>
        <article class="stat-card"><span class="stat-symbol">✧</span><div><strong>{{ $articleCount }}</strong><span>Journal stories</span></div></article>
        <article class="stat-card">
            <span class="stat-symbol">◇</span><div><strong>{{ $enquiryCount }}</strong><span>Total enquiries</span></div>
            @if ($newEnquiryCount > 0)<small>{{ $newEnquiryCount }} new</small>@endif
        </article>
    </section>

    <section class="dashboard-section">
        <div class="section-title-row">
            <div>
                <span class="admin-eyebrow">SETTINGS</span>
                <h2>Page settings</h2>
                <p>Select a page from the sidebar or below to manage its public content.</p>
            </div>
            <span class="phase-badge">Content tools · Active</span>
        </div>

        <div class="page-settings-grid">
            <article class="page-setting-card" id="page-home">
                <div class="page-thumb"><img src="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_6.jpg') }}" alt=""></div>
                <div class="page-setting-copy">
                    <span class="admin-eyebrow">LANDING PAGE</span><h3>Home page</h3>
                    <p>Hero, featured experiences, planning and journal sections.</p>
                    <div><a href="{{ route('home') }}" target="_blank" rel="noopener">Preview ↗</a><a class="customize-link" href="{{ route('admin.pages.home.edit') }}">Customize →</a></div>
                </div>
            </article>

            @foreach ($pages as $slug => $page)
                <article class="page-setting-card" id="page-{{ $slug }}">
                    <div class="page-thumb"><img src="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_'.$page['image'].'.jpg') }}" alt=""></div>
                    <div class="page-setting-copy">
                        <span class="admin-eyebrow">{{ $page['kicker'] }}</span><h3>{{ ucwords(str_replace('-', ' ', $slug)) }}</h3>
                        <p>{{ $page['title'] }}</p>
                        <div>
                            <a href="{{ route('page', $slug) }}" target="_blank" rel="noopener">Preview ↗</a>
                            @if (in_array($slug, config('park.customizable_pages')))
                                <a class="customize-link" href="{{ route('admin.pages.'.$slug.'.edit') }}">Customize →</a>
                            @else
                                <button type="button" disabled>Customize soon</button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
