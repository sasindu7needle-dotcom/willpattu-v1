<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveWildlifeContentItemRequest;
use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Http\RedirectResponse;

class WildlifeContentItemController extends Controller
{
    public function store(SaveWildlifeContentItemRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('wildlife');
        $position = ((int) $pageContent->items()->reorder()->max('position')) + 1;

        $pageContent->items()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return to_route('admin.pages.wildlife.edit')->with('status', 'Wildlife section added successfully.');
    }

    public function update(SaveWildlifeContentItemRequest $request, PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToWildlifePage($pageContentItem);
        $pageContentItem->update($request->validated());

        return to_route('admin.pages.wildlife.edit')->with('status', 'Wildlife section updated successfully.');
    }

    public function destroy(PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToWildlifePage($pageContentItem);
        $pageContentItem->delete();

        return to_route('admin.pages.wildlife.edit')->with('status', 'Wildlife section deleted successfully.');
    }

    private function ensureBelongsToWildlifePage(PageContentItem $pageContentItem): void
    {
        abort_unless($pageContentItem->pageContent()->where('slug', 'wildlife')->exists(), 404);
    }
}
