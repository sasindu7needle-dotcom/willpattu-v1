<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveGalleryContentItemRequest;
use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Http\RedirectResponse;

class GalleryContentItemController extends Controller
{
    public function store(SaveGalleryContentItemRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('gallery');
        $position = ((int) $pageContent->items()->reorder()->max('position')) + 1;

        $pageContent->items()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return to_route('admin.pages.gallery.edit')->with('status', 'Gallery photograph added successfully.');
    }

    public function update(SaveGalleryContentItemRequest $request, PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToGalleryPage($pageContentItem);
        $pageContentItem->update($request->validated());

        return to_route('admin.pages.gallery.edit')->with('status', 'Gallery photograph updated successfully.');
    }

    public function destroy(PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToGalleryPage($pageContentItem);
        $pageContentItem->delete();

        return to_route('admin.pages.gallery.edit')->with('status', 'Gallery photograph deleted successfully.');
    }

    private function ensureBelongsToGalleryPage(PageContentItem $pageContentItem): void
    {
        abort_unless($pageContentItem->pageContent()->where('slug', 'gallery')->exists(), 404);
    }
}
