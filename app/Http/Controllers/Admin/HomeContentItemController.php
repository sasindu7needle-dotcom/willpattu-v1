<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveHomeContentItemRequest;
use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Http\RedirectResponse;

class HomeContentItemController extends Controller
{
    public function store(SaveHomeContentItemRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('home');
        $position = ((int) $pageContent->items()->reorder()->max('position')) + 1;

        $pageContent->items()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return to_route('admin.pages.home.edit')->with('status', 'Discovery card added successfully.');
    }

    public function update(SaveHomeContentItemRequest $request, PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToHomePage($pageContentItem);
        $pageContentItem->update($request->validated());

        return to_route('admin.pages.home.edit')->with('status', 'Discovery card updated successfully.');
    }

    public function destroy(PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToHomePage($pageContentItem);
        $pageContentItem->delete();

        return to_route('admin.pages.home.edit')->with('status', 'Discovery card deleted successfully.');
    }

    private function ensureBelongsToHomePage(PageContentItem $pageContentItem): void
    {
        abort_unless($pageContentItem->pageContent()->where('slug', 'home')->exists(), 404);
    }
}
