<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveOfferContentItemRequest;
use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Http\RedirectResponse;

class OfferContentItemController extends Controller
{
    public function store(SaveOfferContentItemRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('offers');
        $position = ((int) $pageContent->items()->reorder()->max('position')) + 1;

        $pageContent->items()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return to_route('admin.pages.offers.edit')->with('status', 'Offer added successfully.');
    }

    public function update(SaveOfferContentItemRequest $request, PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToOffersPage($pageContentItem);
        $pageContentItem->update($request->validated());

        return to_route('admin.pages.offers.edit')->with('status', 'Offer updated successfully.');
    }

    public function destroy(PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToOffersPage($pageContentItem);
        $pageContentItem->delete();

        return to_route('admin.pages.offers.edit')->with('status', 'Offer deleted successfully.');
    }

    private function ensureBelongsToOffersPage(PageContentItem $pageContentItem): void
    {
        abort_unless($pageContentItem->pageContent()->where('slug', 'offers')->exists(), 404);
    }
}
