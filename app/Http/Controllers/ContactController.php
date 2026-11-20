<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendContactMessageRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(SiteSettings $settings): View
    {
        return view('public.contact', ['settings' => $settings->localized()]);
    }

    public function store(SendContactMessageRequest $request, SiteSettings $settings): RedirectResponse
    {
        $message = ContactMessage::create($request->safe()->except('website'));
        $recipient = $settings->all()['email'] ?? config('mail.from.address');

        Mail::to($recipient)->send(new ContactMessageReceived($message));

        return back()->with('status', __('site.contact_sent'));
    }
}
