<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateExperiencePageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ExperiencePageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('experiences');

        return view('admin.pages.editor', [
            'pageContent' => $pageContent->load('items'),
            'imageOptions' => config('park.content_images'),
            'pageName' => 'Experiences',
            'publicSlug' => 'experiences',
            'routePrefix' => 'admin.pages.experiences',
            'sectionEyebrow' => 'BEYOND THE SAFARI',
            'sectionName' => 'Experience',
            'sectionNamePlural' => 'Experiences',
            'sectionDescription' => 'activities and excursions',
            'titlePlaceholder' => 'e.g. Guided birdwatching',
            'metaPlaceholder' => 'e.g. By arrangement · Half day',
        ]);
    }

    public function update(UpdateExperiencePageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('experiences')->update($request->validated());

        return to_route('admin.pages.experiences.edit')->with('status', 'Experiences page details updated successfully.');
    }
}
