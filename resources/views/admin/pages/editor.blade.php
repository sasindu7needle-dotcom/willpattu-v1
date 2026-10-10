@extends('admin.layout')

@section('title', 'Customize '.$pageName.' Page')

@section('content')
    <section class="editor-heading">
        <div>
            <a class="admin-breadcrumb" href="{{ route('admin.dashboard') }}">Dashboard / Page settings / {{ $pageName }}</a>
            <span class="admin-eyebrow">PAGE CUSTOMIZATION</span>
            <h1>{{ $pageName }} <em>page.</em></h1>
            <p>Update the introduction and manage the {{ $sectionDescription }} shown to guests.</p>
        </div>
        <a class="outline-button" href="{{ $publicUrl ?? route('page', $publicSlug) }}" target="_blank" rel="noopener">Preview {{ $pageName }} page <span>↗</span></a>
    </section>

    @if (session('status'))
        <div class="editor-notice success" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="editor-notice error" role="alert"><strong>Please review the highlighted information.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="editor-panel">
        <div class="editor-panel-heading">
            <div><span class="admin-eyebrow">PAGE INTRODUCTION</span><h2>Hero content</h2></div>
            <span>Visible at the top of the {{ $pageName }} page</span>
        </div>
        <div class="hero-editor-grid">
            <div class="editor-image-preview hero-preview"><img id="hero-preview" src="{{ isset($fixedHeroImage) ? asset($fixedHeroImage) : asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_'.$pageContent->hero_image.'.jpg') }}" alt="Current {{ $pageName }} page hero image"><span>{{ isset($fixedHeroImage) ? 'Animation background' : 'Hero image' }}</span></div>
            <form class="admin-editor-form" method="POST" action="{{ route($routePrefix.'.update') }}">
                @csrf
                @method('PUT')
                <div class="editor-field-grid">
                    <label>Section label<input name="kicker" value="{{ old('kicker', $pageContent->kicker) }}" maxlength="120" required></label>
                    @if (! isset($fixedHeroImage))
                        <label>Hero image
                            <select name="hero_image" required data-image-select data-image-target="hero-preview" data-image-prefix="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_') }}" data-image-suffix=".jpg">
                                @foreach ($imageOptions as $image)<option value="{{ $image }}" @selected((int) old('hero_image', $pageContent->hero_image) === $image)>Wilpattu photograph {{ $image }}</option>@endforeach
                            </select>
                        </label>
                    @endif
                </div>
                <label>Page title<input name="title" value="{{ old('title', $pageContent->title) }}" maxlength="160" required></label>
                <label>Introduction<textarea name="intro" rows="4" maxlength="1000" required>{{ old('intro', $pageContent->intro) }}</textarea></label>
                <div class="form-actions"><button class="admin-button compact" type="submit">Save page details <span>→</span></button></div>
            </form>
        </div>
    </section>

    @if (isset($animationSettings))
        <section class="editor-panel animation-editor-panel">
            <div class="editor-panel-heading">
                <div><span class="admin-eyebrow">HOMEPAGE MOTION</span><h2>Animal animation</h2></div>
                <span>12-frame sprite atlas</span>
            </div>
            <div class="animation-editor-grid">
                <div class="animation-preview-wrap">
                    <div class="animation-preview-stage">
                        <div class="animation-sprite-preview" data-animation-preview style="background-image: url('{{ $animationUrl }}')"></div>
                    </div>
                    <span class="animation-preview-label" data-animation-preview-label>{{ $animationSettings['animation_path'] ? 'Custom animation' : 'Default leopard animation' }}</span>
                </div>
                <div>
                    <form class="admin-editor-form" method="POST" action="{{ route('admin.pages.home.animation.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="animation_enabled" value="0">
                        <label class="animation-toggle"><input type="checkbox" name="animation_enabled" value="1" @checked(old('animation_enabled', $animationSettings['animation_enabled']))><span><strong>Show animal animation</strong><small>Turn off the animal while keeping the homepage scroll transition.</small></span></label>
                        <label>Accessible animal description<input name="animation_alt" value="{{ old('animation_alt', $animationSettings['animation_alt']) }}" maxlength="160" required><small>Describe the animal for guests using assistive technology.</small></label>
                        <label>Replacement sprite atlas<input type="file" name="animation_file" accept="image/png" data-animation-file><small>PNG only, maximum 12 MB. The width must divide into 4 columns and the height into 3 rows.</small></label>
                        <div class="form-actions"><button class="admin-button compact" type="submit">Save animation <span>→</span></button></div>
                    </form>
                    @if ($animationSettings['animation_path'])
                        <form class="animation-reset-form" method="POST" action="{{ route('admin.pages.home.animation.reset') }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-action" type="submit">Restore default leopard animation</button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="editor-panel">
        <div class="editor-panel-heading">
            <div><span class="admin-eyebrow">{{ $sectionEyebrow }}</span><h2>{{ $sectionNamePlural }}</h2></div>
            <span>{{ $pageContent->items->count() }} {{ Str::plural(strtolower($sectionName), $pageContent->items->count()) }}</span>
        </div>

        <div class="content-editor-list">
            @forelse ($pageContent->items as $item)
                <article class="content-editor-card">
                    <div class="editor-image-preview"><img id="item-preview-{{ $item->id }}" src="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_'.$item->image.'.jpg') }}" alt=""><span>Section {{ $loop->iteration }}</span></div>
                    <div class="content-editor-body">
                        <form class="admin-editor-form" method="POST" action="{{ route($routePrefix.'.items.update', $item) }}">
                            @csrf
                            @method('PUT')
                            <div class="editor-field-grid">
                                <label>{{ $titleFieldLabel ?? 'Title' }}<input name="title" value="{{ $item->title }}" maxlength="160" required></label>
                                <label>{{ $metaFieldLabel ?? 'Short label' }}<input name="meta" value="{{ $item->meta }}" maxlength="160" required></label>
                            </div>
                            @if (isset($destinationOptions))
                                <label>{{ $textFieldLabel ?? 'Destination page' }}<select name="text" required>@foreach ($destinationOptions as $slug => $label)<option value="{{ $slug }}" @selected($item->text === $slug)>{{ $label }}</option>@endforeach</select></label>
                            @else
                                <label>{{ $textFieldLabel ?? 'Description' }}<textarea name="text" rows="3" maxlength="1500" required>{{ $item->text }}</textarea></label>
                            @endif
                            <label>Photograph
                                <select name="image" required data-image-select data-image-target="item-preview-{{ $item->id }}" data-image-prefix="{{ asset('images/Makara_resorts_alankuda_beach_excursion_wilpattu_') }}" data-image-suffix=".jpg">
                                    @foreach ($imageOptions as $image)<option value="{{ $image }}" @selected($item->image === $image)>Wilpattu photograph {{ $image }}</option>@endforeach
                                </select>
                            </label>
                            <div class="form-actions"><button class="text-action" type="submit">Save changes →</button></div>
                        </form>
                        <form class="delete-form" method="POST" action="{{ route($routePrefix.'.items.destroy', $item) }}" data-confirm="{{ $item->title }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete section</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="empty-editor-state"><span>◇</span><h3>No {{ strtolower($sectionNamePlural) }} yet</h3><p>Add the first {{ strtolower($sectionName) }} below.</p></div>
            @endforelse
        </div>
    </section>

    <section class="editor-panel add-section-panel">
        <div class="editor-panel-heading">
            <div><span class="admin-eyebrow">ADD CONTENT</span><h2>New {{ $sectionName }}</h2></div>
            <span>Creates a new card on the public page</span>
        </div>
        <form class="admin-editor-form add-item-form" method="POST" action="{{ route($routePrefix.'.items.store') }}">
            @csrf
            <div class="editor-field-grid">
                <label>{{ $titleFieldLabel ?? 'Title' }}<input name="title" value="{{ old('title') }}" maxlength="160" placeholder="{{ $titlePlaceholder }}" required></label>
                <label>{{ $metaFieldLabel ?? 'Short label' }}<input name="meta" value="{{ old('meta') }}" maxlength="160" placeholder="{{ $metaPlaceholder }}" required></label>
            </div>
            @if (isset($destinationOptions))
                <label>{{ $textFieldLabel ?? 'Destination page' }}<select name="text" required>@foreach ($destinationOptions as $slug => $label)<option value="{{ $slug }}" @selected(old('text') === $slug)>{{ $label }}</option>@endforeach</select></label>
            @else
                <label>{{ $textFieldLabel ?? 'Description' }}<textarea name="text" rows="4" maxlength="1500" placeholder="Describe this {{ strtolower($sectionName) }}..." required>{{ old('text') }}</textarea></label>
            @endif
            <label>Photograph<select name="image" required>@foreach ($imageOptions as $image)<option value="{{ $image }}" @selected((int) old('image', 13) === $image)>Wilpattu photograph {{ $image }}</option>@endforeach</select></label>
            <div class="form-actions"><button class="admin-button compact" type="submit">Add {{ $sectionName }} <span>+</span></button></div>
        </form>
    </section>
@endsection
