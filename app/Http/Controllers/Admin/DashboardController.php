<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'pages' => config('content.pages'),
            'pageCount' => count(config('content.pages')) + 1,
            'articleCount' => count(config('content.articles')),
            'enquiryCount' => DB::table('enquiries')->count(),
            'newEnquiryCount' => DB::table('enquiries')->where('status', 'new')->count(),
        ]);
    }
}
