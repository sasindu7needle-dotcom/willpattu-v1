<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactPageRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactPageController extends Controller
{
    public function edit(): View
    {
        $pageContent = PageContent::firstOrCreateFromConfiguration('contact');

        return view('admin.pages.contact', [
            'pageContent' => $pageContent,
            'imageOptions' => config('park.content_images'),
        ]);
    }

    public function update(UpdateContactPageRequest $request): RedirectResponse
    {
        PageContent::firstOrCreateFromConfiguration('contact')->update($request->validated());

        return to_route('admin.pages.contact.edit')->with('status', 'Contact page content updated successfully.');
    }
}
