<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDiningPageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DiningPageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('dining');

        return view('admin.pages.editor', [
            'pageContent' => $pageContent->load('items'),
            'imageOptions' => config('park.content_images'),
            'pageName' => 'Dining',
            'publicSlug' => 'dining',
            'routePrefix' => 'admin.pages.dining',
            'sectionEyebrow' => 'DINING EXPERIENCES',
            'sectionName' => 'Dining section',
            'sectionNamePlural' => 'Dining sections',
            'sectionDescription' => 'dining concepts and meal experiences',
            'titlePlaceholder' => 'e.g. A Sri Lankan feast',
            'metaPlaceholder' => 'e.g. Dinner · By arrangement',
        ]);
    }

    public function update(UpdateDiningPageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('dining')->update($request->validated());

        return to_route('admin.pages.dining.edit')->with('status', 'Dining page details updated successfully.');
    }
}
