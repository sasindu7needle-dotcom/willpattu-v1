<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParkController extends Controller
{
    public function home(): View
    {
        return view('home', ['pages' => config('content.pages')]);
    }

    public function page(string $slug): View
    {
        $page = config('content.pages.'.$slug);
        abort_unless($page, 404);

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
}
