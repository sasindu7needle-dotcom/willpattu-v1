<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveStayContentItemRequest;
use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Http\RedirectResponse;

class StayContentItemController extends Controller
{
    public function store(SaveStayContentItemRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('stay');
        $position = ((int) $pageContent->items()->reorder()->max('position')) + 1;

        $pageContent->items()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return to_route('admin.pages.stay.edit')->with('status', 'Accommodation section added successfully.');
    }

    public function update(SaveStayContentItemRequest $request, PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToStayPage($pageContentItem);
        $pageContentItem->update($request->validated());

        return to_route('admin.pages.stay.edit')->with('status', 'Accommodation section updated successfully.');
    }

    public function destroy(PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToStayPage($pageContentItem);
        $pageContentItem->delete();

        return to_route('admin.pages.stay.edit')->with('status', 'Accommodation section deleted successfully.');
    }

    private function ensureBelongsToStayPage(PageContentItem $pageContentItem): void
    {
        abort_unless($pageContentItem->pageContent()->where('slug', 'stay')->exists(), 404);
    }
}
