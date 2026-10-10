<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveDiningContentItemRequest;
use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Http\RedirectResponse;

class DiningContentItemController extends Controller
{
    public function store(SaveDiningContentItemRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('dining');
        $position = ((int) $pageContent->items()->reorder()->max('position')) + 1;

        $pageContent->items()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return to_route('admin.pages.dining.edit')->with('status', 'Dining section added successfully.');
    }

    public function update(SaveDiningContentItemRequest $request, PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToDiningPage($pageContentItem);
        $pageContentItem->update($request->validated());

        return to_route('admin.pages.dining.edit')->with('status', 'Dining section updated successfully.');
    }

    public function destroy(PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToDiningPage($pageContentItem);
        $pageContentItem->delete();

        return to_route('admin.pages.dining.edit')->with('status', 'Dining section deleted successfully.');
    }

    private function ensureBelongsToDiningPage(PageContentItem $pageContentItem): void
    {
        abort_unless($pageContentItem->pageContent()->where('slug', 'dining')->exists(), 404);
    }
}
