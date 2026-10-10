<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveExperienceContentItemRequest;
use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Http\RedirectResponse;

class ExperienceContentItemController extends Controller
{
    public function store(SaveExperienceContentItemRequest $request): RedirectResponse
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('experiences');
        $position = ((int) $pageContent->items()->reorder()->max('position')) + 1;

        $pageContent->items()->create([
            ...$request->validated(),
            'position' => $position,
        ]);

        return to_route('admin.pages.experiences.edit')->with('status', 'Experience added successfully.');
    }

    public function update(SaveExperienceContentItemRequest $request, PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToExperiencesPage($pageContentItem);
        $pageContentItem->update($request->validated());

        return to_route('admin.pages.experiences.edit')->with('status', 'Experience updated successfully.');
    }

    public function destroy(PageContentItem $pageContentItem): RedirectResponse
    {
        $this->ensureBelongsToExperiencesPage($pageContentItem);
        $pageContentItem->delete();

        return to_route('admin.pages.experiences.edit')->with('status', 'Experience deleted successfully.');
    }

    private function ensureBelongsToExperiencesPage(PageContentItem $pageContentItem): void
    {
        abort_unless($pageContentItem->pageContent()->where('slug', 'experiences')->exists(), 404);
    }
}
