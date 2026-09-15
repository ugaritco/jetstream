<?php

namespace Ugarit\Jetstream\Http\Controllers\Livewire;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Heritage\Support\Str;
use Ugarit\Jetstream\Jetstream;

class TermsOfServiceController extends Controller
{
    /**
     * Show the terms of service for the application.
     *
     * @param  \Heritage\Http\Request  $request
     * @return \Heritage\View\View
     */
    public function show(Request $request)
    {
        $termsFile = Jetstream::localizedMarkdownPath('terms.md');

        return view('terms', [
            'terms' => Str::markdown(file_get_contents($termsFile)),
        ]);
    }
}
