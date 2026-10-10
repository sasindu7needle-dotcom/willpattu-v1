@extends('admin.layout')

@section('title', 'Customize Contact Page')

@section('content')
    @php($settings = old('settings', $pageContent->settings ?? config('content.pages.contact.settings')))

    <section class="editor-heading">
        <div>
            <a class="admin-breadcrumb" href="{{ route('admin.dashboard') }}">Dashboard / Page settings / Contact</a>
            <span class="admin-eyebrow">PAGE CUSTOMIZATION</span>
            <h1>Contact <em>page.</em></h1>
            <p>Update the page introduction and supporting copy around the protected enquiry form.</p>
        </div>
        <a class="outline-button" href="{{ route('page', 'contact') }}" target="_blank" rel="noopener">Preview Contact page <span>↗</span></a>
    </section>

    @if (session('status'))
        <div class="editor-notice success" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="editor-notice error" role="alert"><strong>Please review the highlighted information.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form class="admin-editor-form" method="POST" action="{{ route('admin.pages.contact.update') }}">
        @csrf
        @method('PUT')

        <section class="editor-panel">
            <div class="editor-panel-heading">
                <div><span class="admin-eyebrow">PAGE INTRODUCTION</span><h2>Hero content</h2></div>
                <span>Visible at the top of the Contact page</span>
            </div>
            <div class="hero-editor-grid">
                <div class="editor-image-preview hero-preview"><img id="hero-preview" src="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_'.$pageContent->hero_image.'.jpg') }}" alt="Current Contact page hero image"><span>Hero image</span></div>
                <div>
                    <div class="editor-field-grid">
                        <label>Section label<input name="kicker" value="{{ old('kicker', $pageContent->kicker) }}" maxlength="120" required></label>
                        <label>Hero image
                            <select name="hero_image" required data-image-select data-image-target="hero-preview" data-image-prefix="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_') }}" data-image-suffix=".jpg">
                                @foreach ($imageOptions as $image)<option value="{{ $image }}" @selected((int) old('hero_image', $pageContent->hero_image) === $image)>Wilpattu photograph {{ $image }}</option>@endforeach
                            </select>
                        </label>
                    </div>
                    <label>Page title<input name="title" value="{{ old('title', $pageContent->title) }}" maxlength="160" required></label>
                    <label>Introduction<textarea name="intro" rows="4" maxlength="1000" required>{{ old('intro', $pageContent->intro) }}</textarea></label>
                </div>
            </div>
        </section>

        <section class="editor-panel">
            <div class="editor-panel-heading">
                <div><span class="admin-eyebrow">CONTACT COPY</span><h2>Supporting text</h2></div>
                <span>Text surrounding the enquiry form</span>
            </div>
            <div class="contact-copy-grid">
                <div>
                    <label>Left-column label<input name="settings[eyebrow]" value="{{ $settings['eyebrow'] }}" maxlength="120" required></label>
                    <label>Left-column heading<textarea name="settings[heading]" rows="3" maxlength="200" required>{{ $settings['heading'] }}</textarea><small>Line breaks are preserved on the public page.</small></label>
                    <label>Supporting paragraph<textarea name="settings[supporting_text]" rows="4" maxlength="1000" required>{{ $settings['supporting_text'] }}</textarea></label>
                </div>
                <div>
                    <label>Reservations label<input name="settings[reservations_label]" value="{{ $settings['reservations_label'] }}" maxlength="120" required></label>
                    <label>Availability note<textarea name="settings[availability_note]" rows="4" maxlength="1000" required>{{ $settings['availability_note'] }}</textarea></label>
                    <label>Submission note<textarea name="settings[submission_note]" rows="3" maxlength="500" required>{{ $settings['submission_note'] }}</textarea></label>
                </div>
            </div>
        </section>

        <section class="protected-form-panel">
            <span class="protected-form-icon" aria-hidden="true">◇</span>
            <div><span class="admin-eyebrow">PROTECTED FORM</span><h3>Enquiry fields stay unchanged.</h3><p>Name, email, phone, enquiry type, dates, travellers, interests, message, consent, validation, and submission handling are not editable here.</p></div>
        </section>

        <div class="contact-save-bar"><button class="admin-button compact" type="submit">Save Contact page <span>→</span></button></div>
    </form>
@endsection
