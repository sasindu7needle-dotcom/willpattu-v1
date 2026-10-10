<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeAnimationRequest;
use App\Http\Requests\Admin\UpdateHomePageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class HomePageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('home');
        $animationSettings = array_replace(
            config('content.home.settings'),
            $pageContent->settings ?? [],
        );
        $animationUrl = $animationSettings['animation_path']
            ? Storage::disk('public')->url($animationSettings['animation_path'])
            : asset('images/leopard-front-walk-atlas.png');

        return view('admin.pages.editor', [
            'pageContent' => $pageContent->load('items'),
            'imageOptions' => config('park.content_images'),
            'pageName' => 'Home',
            'publicSlug' => 'home',
            'publicUrl' => route('home'),
            'routePrefix' => 'admin.pages.home',
            'sectionEyebrow' => 'FEATURED EXPERIENCES',
            'sectionName' => 'Discovery card',
            'sectionNamePlural' => 'Discovery cards',
            'sectionDescription' => 'featured discovery cards',
            'titlePlaceholder' => 'e.g. Private safaris',
            'metaPlaceholder' => 'e.g. Into the wilderness',
            'metaFieldLabel' => 'Caption',
            'textFieldLabel' => 'Destination page',
            'fixedHeroImage' => 'images/wilpattu-forest-plate.png',
            'animationSettings' => $animationSettings,
            'animationUrl' => $animationUrl,
            'destinationOptions' => collect(config('content.pages'))
                ->mapWithKeys(fn (array $page, string $slug): array => [
                    $slug => ucwords(str_replace('-', ' ', $slug)),
                ])->all(),
        ]);
    }

    public function update(UpdateHomePageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('home')->update($request->validated());

        return to_route('admin.pages.home.edit')->with('status', 'Home page details updated successfully.');
    }

    public function updateAnimation(UpdateHomeAnimationRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('home');
        $settings = array_replace(config('content.home.settings'), $pageContent->settings ?? []);
        $previousAnimationPath = $settings['animation_path'];
        $validated = $request->validated();

        $settings['animation_enabled'] = $request->boolean('animation_enabled');
        $settings['animation_alt'] = $validated['animation_alt'];

        if ($request->hasFile('animation_file')) {
            $settings['animation_path'] = $request->file('animation_file')->store('home-animations', 'public');
        }

        $pageContent->update(['settings' => $settings]);

        if ($settings['animation_path'] !== $previousAnimationPath && $previousAnimationPath) {
            Storage::disk('public')->delete($previousAnimationPath);
        }

        return to_route('admin.pages.home.edit')->with('status', 'Homepage animal animation updated successfully.');
    }

    public function resetAnimation(): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('home');
        $settings = array_replace(config('content.home.settings'), $pageContent->settings ?? []);

        if ($settings['animation_path']) {
            Storage::disk('public')->delete($settings['animation_path']);
        }

        $pageContent->update(['settings' => config('content.home.settings')]);

        return to_route('admin.pages.home.edit')->with('status', 'Default leopard animation restored successfully.');
    }
}
