<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWildlifePageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class WildlifePageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('wildlife');

        return view('admin.pages.editor', [
            'pageContent' => $pageContent->load('items'),
            'imageOptions' => config('park.content_images'),
            'pageName' => 'Wildlife',
            'publicSlug' => 'wildlife',
            'routePrefix' => 'admin.pages.wildlife',
            'sectionEyebrow' => 'WILDLIFE & HABITATS',
            'sectionName' => 'Wildlife section',
            'sectionNamePlural' => 'Wildlife sections',
            'sectionDescription' => 'wildlife and habitat sections',
            'titlePlaceholder' => 'e.g. Sri Lankan leopard',
            'metaPlaceholder' => 'e.g. Wildlife · Big cats',
        ]);
    }

    public function update(UpdateWildlifePageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('wildlife')->update($request->validated());

        return to_route('admin.pages.wildlife.edit')->with('status', 'Wildlife page details updated successfully.');
    }
}
