<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStayPageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StayPageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('stay');

        return view('admin.pages.editor', [
            'pageContent' => $pageContent->load('items'),
            'imageOptions' => config('park.content_images'),
            'pageName' => 'Stay',
            'publicSlug' => 'stay',
            'routePrefix' => 'admin.pages.stay',
            'sectionEyebrow' => 'ACCOMMODATION CONTENT',
            'sectionName' => 'Stay section',
            'sectionNamePlural' => 'Stay sections',
            'sectionDescription' => 'accommodation sections',
            'titlePlaceholder' => 'e.g. Standard Tent',
            'metaPlaceholder' => 'e.g. 40 sqm · Up to 3 adults',
        ]);
    }

    public function update(UpdateStayPageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('stay')->update($request->validated());

        return to_route('admin.pages.stay.edit')->with('status', 'Stay page details updated successfully.');
    }
}
