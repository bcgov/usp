<?php

namespace Modules\Institution\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function faqList(Request $request): \Inertia\Response
    {
        $faqs = Faq::where('active_status', true)->orderBy('order', 'asc')->get();

        return Inertia::render('Institution::Faq', ['status' => true, 'results' => $faqs,]);
    }
}
