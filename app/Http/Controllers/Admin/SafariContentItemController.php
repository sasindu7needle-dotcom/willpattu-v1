<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveSafariContentItemRequest;
use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Http\RedirectResponse;

class SafariContentItemController extends Controller
{
    public function store(SaveSafariContentItemRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('safaris');
        $position = ((int) $pageContent->items()->reorder()->max('position')) + 1;

        $pageContent->items()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return to_route('admin.pages.safaris.edit')->with('status', 'Safari experience added successfully.');
    }

    public function update(SaveSafariContentItemRequest $request, PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToSafarisPage($pageContentItem);
        $pageContentItem->update($request->validated());

        return to_route('admin.pages.safaris.edit')->with('status', 'Safari experience updated successfully.');
    }

    public function destroy(PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToSafarisPage($pageContentItem);
        $pageContentItem->delete();

        return to_route('admin.pages.safaris.edit')->with('status', 'Safari experience deleted successfully.');
    }

    private function ensureBelongsToSafarisPage(PageContentItem $pageContentItem): void
    {
        abort_unless($pageContentItem->pageContent()->where('slug', 'safaris')->exists(), 404);
    }
}
