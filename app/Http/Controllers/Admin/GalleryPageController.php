<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateGalleryPageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class GalleryPageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('gallery');

        return view('admin.pages.editor', [
            'pageContent' => $pageContent->load('items'),
            'imageOptions' => config('park.content_images'),
            'pageName' => 'Gallery',
            'publicSlug' => 'gallery',
            'routePrefix' => 'admin.pages.gallery',
            'sectionEyebrow' => 'GALLERY PHOTOGRAPHS',
            'sectionName' => 'Photograph',
            'sectionNamePlural' => 'Gallery photographs',
            'sectionDescription' => 'photographs and filter categories',
            'titlePlaceholder' => 'e.g. Leopard on a forest trail',
            'metaPlaceholder' => 'e.g. Wildlife',
            'titleFieldLabel' => 'Caption',
            'metaFieldLabel' => 'Filter category',
            'textFieldLabel' => 'Image description',
        ]);
    }

    public function update(UpdateGalleryPageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('gallery')->update($request->validated());

        return to_route('admin.pages.gallery.edit')->with('status', 'Gallery page details updated successfully.');
    }
}
