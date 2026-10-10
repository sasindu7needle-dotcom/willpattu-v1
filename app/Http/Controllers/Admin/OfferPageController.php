<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOfferPageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OfferPageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('offers');

        return view('admin.pages.editor', [
            'pageContent' => $pageContent->load('items'),
            'imageOptions' => config('park.content_images'),
            'pageName' => 'Offers',
            'publicSlug' => 'offers',
            'routePrefix' => 'admin.pages.offers',
            'sectionEyebrow' => 'PACKAGES & SPECIAL OFFERS',
            'sectionName' => 'Offer',
            'sectionNamePlural' => 'Offers',
            'sectionDescription' => 'packages and special offers',
            'titlePlaceholder' => 'e.g. The complete safari escape',
            'metaPlaceholder' => 'e.g. Couples · Quotation on request',
        ]);
    }

    public function update(UpdateOfferPageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('offers')->update($request->validated());

        return to_route('admin.pages.offers.edit')->with('status', 'Offers page details updated successfully.');
    }
}
