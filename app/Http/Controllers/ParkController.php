<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Http\Requests\SubscribeNewsletterRequest;
use App\Models\PageContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParkController extends Controller
{
    public function home(): View
    {
        $page = config('content.home');
        $customPage = PageContent::query()->with('items')->where('slug', 'home')->first();

        if ($customPage) {
            $page = [
                'title' => $customPage->title,
                'kicker' => $customPage->kicker,
                'intro' => $customPage->intro,
                'image' => $customPage->hero_image,
                'settings' => $customPage->settings ?? $page['settings'],
                'items' => $customPage->items->map(fn ($item): array => [
                    'title' => $item->title,
                    'meta' => $item->meta,
                    'text' => $item->text,
                    'image' => $item->image,
                ])->all(),
            ];
        }

        $animationSettings = array_replace(config('content.home.settings'), $page['settings'] ?? []);
        $animationUrl = $animationSettings['animation_path']
            ? Storage::disk('public')->url($animationSettings['animation_path'])
            : asset('images/leopard-front-walk-atlas.png');

        return view('home', compact('page', 'animationSettings', 'animationUrl'));
    }

    public function page(string $slug): View
    {
        $page = config('content.pages.'.$slug);
        abort_unless($page, 404);

        $customPage = PageContent::query()->with('items')->where('slug', $slug)->first();

        if ($customPage) {
            $page = [
                'title' => $customPage->title,
                'kicker' => $customPage->kicker,
                'intro' => $customPage->intro,
                'image' => $customPage->hero_image,
                'settings' => $customPage->settings ?? $page['settings'] ?? [],
                'items' => $customPage->items->map(fn ($item): array => [
                    'title' => $item->title,
                    'meta' => $item->meta,
                    'text' => $item->text,
                    'image' => $item->image,
                ])->all(),
            ];
        }

        return view('page', compact('page', 'slug'));
    }

    public function article(string $slug): View
    {
        $page = config('content.articles.'.$slug);
        abort_unless($page, 404);

        return view('page', ['page' => $page, 'slug' => 'article']);
    }

    public function enquire(StoreEnquiryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['consent', 'website']);
        $data['reference'] = 'WP-'.Str::upper(Str::random(10));
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('enquiries')->insert($data);

        return to_route('page', ['slug' => 'contact'])->with('success', 'Your enquiry has been received. Reference: '.$data['reference'].'. Your booking is subject to confirmation.');
    }

    public function subscribe(SubscribeNewsletterRequest $request): RedirectResponse
    {
        DB::table('newsletter_subscriptions')->insertOrIgnore([
            'email' => Str::lower($request->validated('newsletter_email')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('newsletter_success', 'Thank you for subscribing to our newsletter.')->withFragment('newsletter');
    }
}
