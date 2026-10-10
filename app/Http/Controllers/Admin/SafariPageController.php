<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSafariPageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SafariPageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('safaris');

        return view('admin.pages.editor', [
            'pageContent' => $pageContent->load('items'),
            'imageOptions' => config('park.content_images'),
            'pageName' => 'Safaris',
            'publicSlug' => 'safaris',
            'routePrefix' => 'admin.pages.safaris',
            'sectionEyebrow' => 'SAFARI EXPERIENCES',
            'sectionName' => 'Safari experience',
            'sectionNamePlural' => 'Safari experiences',
            'sectionDescription' => 'safari experiences',
            'titlePlaceholder' => 'e.g. Sunrise safari',
            'metaPlaceholder' => 'e.g. Half-day · Early morning',
        ]);
    }

    public function update(UpdateSafariPageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('safaris')->update($request->validated());

        return to_route('admin.pages.safaris.edit')->with('status', 'Safaris page details updated successfully.');
    }
}
